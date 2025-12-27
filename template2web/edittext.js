document.addEventListener('DOMContentLoaded', function () {
    function addLogout(){
        const logoutLink = document.createElement('a');
        logoutLink.href = 'logout.php';
        logoutLink.style.position = 'fixed';
        logoutLink.style.bottom = '10px';
        logoutLink.style.right = '10px';
        logoutLink.style.display = 'flex';
        logoutLink.style.alignItems = 'center';
        logoutLink.style.textDecoration = 'none';
        logoutLink.style.color = '#fff';
        logoutLink.style.background = '#dc3545';
        logoutLink.style.padding = '10px';
        logoutLink.style.borderRadius = '5px';
        logoutLink.style.zIndex = '99999';

        // Create an icon element (using FontAwesome for example)
        const logoutIcon = document.createElement('i');
        logoutIcon.className = 'fas fa-sign-out-alt'; // Assuming you have FontAwesome included
        logoutIcon.style.marginRight = '5px';

        const logoutText = document.createElement('span');
        logoutText.textContent = 'Logout';

        logoutLink.appendChild(logoutIcon);
        logoutLink.appendChild(logoutText);
        document.body.appendChild(logoutLink);
    }
    addLogout();

    const images = document.querySelectorAll('img');
    images.forEach(img => {
        const button = document.createElement('button');
        button.textContent = `${img.width} x ${img.height} 📤`; // Label with image dimensions and upload icon
        button.style.position = 'absolute';
        button.style.top = '0';
        button.style.left = '0';
        button.style.zIndex = '1000'; // Ensure it's above other content
        button.style.display = 'inline-block';
        button.style.cursor = 'pointer';
        button.style.padding = '2px 5px';
        button.style.fontSize = '12px';
        button.style.marginLeft = '5px';
        button.style.backgroundColor = '#007bff';
        button.style.color = '#fff';
        button.style.border = 'none';
        button.style.borderRadius = '3px';
        button.style.outline = 'none';

        img.parentElement.style.position = 'relative';
        img.insertAdjacentElement('afterend', button);

        button.addEventListener('click', function () {
            const input = document.createElement('input');
            input.type = 'file';
            input.accept = 'image/*';
            input.style.display = 'none';

            input.addEventListener('change', function () {
                const file = input.files[0];
                if (file) {
                    const formData = new FormData();
                    formData.append('width', img.width);
                    formData.append('height', img.height);
                    formData.append('src', img.src);
                    formData.append('image', file);

                    fetch('upload_image.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.text())
                    .then(data => {
                        if (data === 'success') {
                            //alert('Image uploaded and resized successfully.');
                            img.src = img.src + '?v=' + new Date().getTime(); // Refresh image to show the new one
                        } else {
                            alert('Failed to upload and resize image.');
                        }
                    })
                    .catch(() => {
                        alert('Failed to upload. Too Big Size');
                    });
                }
            });

            input.click();
        });
    });
    
    document.addEventListener('click', function (event) {
        const span = event.target.closest('span[data-key]');

        if (span) {
            // Prevent the default action of <a> tag if it contains the span
            event.stopPropagation();
            event.preventDefault();
            makeEditable(span);
        }
    });

    // Delegate the keydown and blur events to the document
    document.addEventListener('keydown', function (event) {
        if (event.key === 'Enter') {
            const span = document.querySelector('span[data-key][contenteditable]');
            if (span) {
                event.preventDefault();
                saveChange(span);
            }
        }
    });

    document.addEventListener('blur', function (event) {
        const span = event.target.closest('span[data-key][contenteditable]');
        if (span) {
            saveChange(span);
        }
    }, true); // Use capture phase for blur event

    function makeEditable(span) {
        if (span.isContentEditable) return; // Prevent entering edit mode if already editing

        span.setAttribute('contenteditable', 'true');
        span.focus(); // Focus on the span after making it editable

        // Apply styling to improve cursor visibility
        span.style.border = '2px solid #007bff'; // Bright blue border
        span.style.outline = 'none'; // Remove default outline
        span.style.color = '#000'; // Black text color for better contrast
        span.style.cursor = 'text'; // Text cursor to indicate editing
        document.querySelector('body').style.setProperty('caret-color', '#007bff'); // Set caret color
        span.style.fontWeight = 'bold'; // Set font weight to bold for better cursor visibility
    }

    function saveChange(span) {
        const key = span.getAttribute('data-key');
        const newValue = span.textContent.trim();

        if (newValue === span.getAttribute('data-original-text')) {
            // If text has not changed, exit edit mode without sending fetch
            exitEditMode(span);
            return;
        }

        // Save the change to lang.php
        fetch('update_lang.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ key: key, value: newValue })
        }).then(response => response.text())
            .then(data => {
                if (data === 'success') {
                    // Successfully saved
                    exitEditMode(span);
                } else {
                    alert('Failed to update language file.');
                }
            }).catch(() => {
                alert('Failed to update language file.');
            });
    }

    function exitEditMode(span) {
        span.removeAttribute('contenteditable'); // Remove contenteditable attribute
        span.style.border = ''; // Remove border style
        span.style.color = ''; // Remove text color
        span.style.cursor = ''; // Reset cursor style
        span.style.fontWeight = ''; // Reset font weight
        span.removeAttribute('data-original-text'); // Remove original text attribute
    }
});
