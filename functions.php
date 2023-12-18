function gc_groups_create_group( $name, $description, $slug, $status, $creator_id ) {
    $args = array(
        'creator_id'  => $creator_id,
        'name'        => $name,
        'description' => $description,
        'slug'        => $slug,
        'status'      => $status,
    );

    $group_id = groups_create_group( $args );

    if ( $group_id ) {
        return $group_id;
    } else {
        return false;
    }
}

function create_groups_from_csv() {
    $csvFileUrl = 'https://gumptioncare.com/wp-content/uploads/2023/12/bb_groups_test.csv';

    $headers = get_headers($csvFileUrl);
    if (strpos($headers[0], '200 OK') !== false) {
        $fileHandle = fopen($csvFileUrl, 'r');
        if ($fileHandle !== false) {
            while (($data = fgetcsv($fileHandle)) !== false) {
                if (count($data) === 5) {
                    gc_groups_create_group($data[0], $data[1], $data[2], $data[3], $data[4]);
                } else {
                    error_log("Invalid CSV row: " . implode(', ', $data));
                }
            }
            fclose($fileHandle);
        } else {
            error_log("Failed to open CSV file: " . $csvFileUrl);
        }
    } else {
        error_log("CSV file not found or not accessible: " . $csvFileUrl);
    }
}

add_action('admin_init', function() {
    if (isset($_GET['import_groups']) && current_user_can('manage_options')) {
        create_groups_from_csv();

        wp_redirect(admin_url('index.php?import_status=success'));
        exit;
    }
});

add_action('admin_notices', function() {
    if (isset($_GET['import_status']) && $_GET['import_status'] == 'success') {
        echo '<div class="notice notice-success is-dismissible"><p>Groups imported successfully.</p></div>';
    }
});

function test_custom_group_creation() {
    $name = "Test Group";
    $description = "This is a test group";
    $slug = "test-group";
    $status = "public";
    $creator_id = 236278622;

    $result = gc_groups_create_group($name, $description, $slug, $status, $creator_id);

    if ($result) {
        echo "Group created successfully. Group ID: " . $result;
    } else {
        echo "Failed to create group.";
    }
}

add_action('admin_init', function() {
    if (isset($_GET['test_create_group'])) {
        test_custom_group_creation();
    }
});
