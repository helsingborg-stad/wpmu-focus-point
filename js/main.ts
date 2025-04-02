class Main {
    constructor(
        private image: HTMLImageElement,
        private focusX: HTMLInputElement,
        private focusY: HTMLInputElement
    ) {
        if (image.complete) {
            this.init();
        } else {
            image.addEventListener("load", () => {
                this.init();
            });
        }
    }

    private init() {
        const [container, imageWrapper, marker] = this.structureMarkup();

        let xPercent = parseFloat(this.focusX.value) || 50;
        let yPercent = parseFloat(this.focusY.value) || 50;
        
        this.updateMarkerPosition(xPercent, yPercent, marker);

        this.image.addEventListener("click", (event) => {
            const rect = this.image.getBoundingClientRect();
            
            const percentX = (event.offsetX / rect.width) * 100;
            const percentY = (event.offsetY / rect.height) * 100;
            
            this.updateMarkerPosition(percentX, percentY, marker);
            this.focusX.value = percentX.toFixed(2);
            this.focusY.value = percentY.toFixed(2);
            this.focusX.dispatchEvent(new Event("change", { bubbles: true }));
            this.focusY.dispatchEvent(new Event("change", { bubbles: true }));
        });
    }

    private updateMarkerPosition(xPercent: number, yPercent: number, marker: HTMLDivElement) {
        marker.style.left = `${xPercent}%`;
        marker.style.top = `${yPercent}%`;
    }

    private structureMarkup() {
        const container = document.createElement('div');
        container.className = 'wpmu-focus-point__container';
        const imageWrapper = document.createElement('div');
        imageWrapper.className = 'wpmu-focus-point__image-wrapper';
        this.image.insertAdjacentElement("beforebegin", container);
        imageWrapper.appendChild(this.image);
        container.appendChild(imageWrapper);
        const marker = document.createElement('div');
        marker.className = 'wpmu-focus-point__marker';
        imageWrapper.appendChild(marker);

        return [container, imageWrapper, marker];
    }
}

export default Main;