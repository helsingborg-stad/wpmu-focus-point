import Main from "./main";

declare const wp: any;

const focusAttribute = "data-js-focus-axis";

document.addEventListener("DOMContentLoaded", () => {
    wp.media.view.Modal.prototype.on("open", () => {
        setTimeout(() => {
            const focusX = document.querySelector(`input[${focusAttribute}="x"]`) as HTMLInputElement;
            const focusY = document.querySelector(`input[${focusAttribute}="y"]`) as HTMLInputElement;
            const image = document.querySelector(`img.details-image:not(.icon)`) as HTMLImageElement;

            if (image && focusX && focusY) {
                const attachmentId = focusX.dataset.attachmentId;
                const attachment = wp.media.attachment(attachmentId);
                console.log(attachment.get("mime"));
                if (
                    !attachment ||
                    !attachment.get('mime') ||
                    !attachment.get('mime').includes("image") ||
                    attachment.get('mime').includes("image/svg+xml")
                ) {
                    return;
                }
                console.log(attachment);
                new Main(image, focusX, focusY);
            }
        }, 300);
    });
});