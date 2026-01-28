document.addEventListener('DOMContentLoaded', function () {
    const imageInput = document.getElementById('hotel_gallery_images');
    const previewContainer = document.getElementById('preview-gallery');
    let allFiles = [];

    imageInput.addEventListener('change', function (event) {
        const files = event.target.files;
        previewContainer.innerHTML = ''; // Clear previous previews
        allFiles = Array.from(files); // Store selected files

        allFiles.forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = function (e) {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.classList.add('carousel-image');
                img.setAttribute('data-index', index);

                const removeButton = document.createElement('button');
                removeButton.textContent = 'Supprimer';
                removeButton.classList.add('remove-image');
                removeButton.onclick = function () {
                    removeImage(index);
                };

                const wrapper = document.createElement('div');
                wrapper.classList.add('carousel-item');
                wrapper.appendChild(img);
                wrapper.appendChild(removeButton);
                previewContainer.appendChild(wrapper);
            };
            reader.readAsDataURL(file);
        });
    });

    function removeImage(index) {
        allFiles.splice(index, 1); // Remove the file from the array
        imageInput.files = createFileList(allFiles); // Update the input files
        previewContainer.innerHTML = ''; // Clear the preview
        allFiles.forEach((file, idx) => {
            const reader = new FileReader();
            reader.onload = function (e) {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.classList.add('carousel-image');
                img.setAttribute('data-index', idx);

                const removeButton = document.createElement('button');
                removeButton.textContent = 'Supprimer';
                removeButton.classList.add('remove-image');
                removeButton.onclick = function () {
                    removeImage(idx);
                };

                const wrapper = document.createElement('div');
                wrapper.classList.add('carousel-item');
                wrapper.appendChild(img);
                wrapper.appendChild(removeButton);
                previewContainer.appendChild(wrapper);
            };
            reader.readAsDataURL(file);
        });
    }

    function createFileList(files) {
        const dataTransfer = new DataTransfer();
        files.forEach(file => dataTransfer.items.add(file));
        return dataTransfer.files;
    }
});