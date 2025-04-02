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
                new Main(image, focusX, focusY);
            }
        }, 300);
    });
});