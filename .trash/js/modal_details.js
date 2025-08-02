function openModal(id) {
    // Create a new XMLHttpRequest to load the form dynamically
    var xhr = new XMLHttpRequest();
    xhr.open("GET", "<?= site_url('laporan_kerja/edit/') ?>" + id, true);
    xhr.onload = function() {
        if (xhr.status == 200) {
            // Inject the content of the modal body
            document.getElementById("modal-body").innerHTML = xhr.responseText;
            document.getElementById("myModal").style.display = "block";
        }
    };
    xhr.send();
}

// Close Modal
function closeModal() {
    document.getElementById("myModal").style.display = "none";
}