<?php
// Get the raw POST data
$data = file_get_contents('php://input');

// Decode the JSON data
$input = json_decode($data, true);

if ($input && isset($input['key']) && isset($input['value'])) {
    $key = $input['key'];
    $value = $input['value'];

    // Path to the lang.php file
    $langFile = 'lang.php';

    // Include the existing dictionary
    include($langFile);

    // Update the dictionary with the new value
    $dictionary[$key] = $value;

    // Save the updated dictionary back to lang.php
    $result = file_put_contents($langFile, "<?php\n\$dictionary = " . var_export($dictionary, true) . ";\n?>");

    if ($result !== false) {
        echo 'success';
    } else {
        echo 'error';
    }
} else {
    echo 'invalid input';
}
?>
