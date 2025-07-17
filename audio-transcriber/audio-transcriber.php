<?php
/*
Plugin Name: Audio Transcriber
Description: Creates a page where users can drag & drop audio files for transcription.
Version: 0.1.0
Author: Codex
*/

// Register activation hook to create page
register_activation_hook(__FILE__, function() {
    $page = array(
        'post_title'   => 'Audio Transcriber',
        'post_name'    => 'audio-transcriber',
        'post_status'  => 'publish',
        'post_type'    => 'page',
        'post_content' => '[audio_transcriber_form]'
    );
    if (!get_page_by_path($page['post_name'])) {
        wp_insert_post($page);
    }
});

// Shortcode to display form
add_shortcode('audio_transcriber_form', function() {
    ob_start();
    ?>
    <div id="at-dropzone" style="border: 2px dashed #ccc; padding: 40px; text-align:center;">
        <p>Drag & drop an audio file here</p>
        <input type="file" id="at-file" accept="audio/*" style="display:none" />
    </div>
    <pre id="at-result"></pre>
    <script>
    const dropzone = document.getElementById('at-dropzone');
    const fileInput = document.getElementById('at-file');
    dropzone.addEventListener('dragover', e => {
        e.preventDefault();
        dropzone.style.background = '#eee';
    });
    dropzone.addEventListener('dragleave', e => {
        dropzone.style.background = '';
    });
    dropzone.addEventListener('drop', e => {
        e.preventDefault();
        dropzone.style.background = '';
        const file = e.dataTransfer.files[0];
        uploadFile(file);
    });
    dropzone.addEventListener('click', () => fileInput.click());
    fileInput.addEventListener('change', () => uploadFile(fileInput.files[0]));

    function uploadFile(file) {
        const formData = new FormData();
        formData.append('action', 'at_transcribe');
        formData.append('audio', file);
        fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
            method: 'POST',
            credentials: 'same-origin',
            body: formData
        })
        .then(r => r.json())
        .then(data => {
            document.getElementById('at-result').textContent = data.text || data.error;
        })
        .catch(e => alert(e));
    }
    </script>
    <?php
    return ob_get_clean();
});

// Handle ajax request
add_action('wp_ajax_at_transcribe', 'at_handle_upload');
add_action('wp_ajax_nopriv_at_transcribe', 'at_handle_upload');
function at_handle_upload() {
    if (empty($_FILES['audio'])) {
        wp_send_json(['error' => 'No file uploaded']);
    }

    $file = wp_handle_upload($_FILES['audio'], ['test_form' => false]);
    if (isset($file['error'])) {
        wp_send_json(['error' => $file['error']]);
    }

    $path = $file['file'];
    $cmd = escapeshellcmd("python3 " . plugin_dir_path(__FILE__) . "transcriber.py " . escapeshellarg($path));
    $output = shell_exec($cmd);

    if (!$output) {
        wp_send_json(['error' => 'Transcription failed']);
    }
    wp_send_json(['text' => $output]);
}
?>
