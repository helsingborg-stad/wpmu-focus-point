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
        const marker = document.createElement('div');    
        marker.className = 'wpmu-focus-point__marker';
        this.image.insertAdjacentElement("beforebegin", marker);

        let x = (parseFloat(this.focusX.value) / 100) * this.image.width || this.image.width / 2;
        let y = (parseFloat(this.focusY.value) / 100) * this.image.height || this.image.height / 2;
        
        this.updateMarkerPosition(x, y, marker);

        this.image.addEventListener("click", (event) => {
            // Calculate percentages (0-100)
            const percentX = Math.round((event.offsetX / this.image.width) * 100);
            const percentY = Math.round((event.offsetY / this.image.height) * 100);
            
            this.updateMarkerPosition(event.offsetX, event.offsetY, marker);
            this.focusX.value = percentX.toString();
            this.focusY.value = percentY.toString();
            this.focusX.dispatchEvent(new Event("change", { bubbles: true }));
            this.focusY.dispatchEvent(new Event("change", { bubbles: true }));
        });
    }

    private updateMarkerPosition(x: number, y: number, marker: HTMLDivElement) {
        const rect = this.image.getBoundingClientRect();
        const relativeX = Math.round((x / this.image.width) * rect.width);
        const relativeY = Math.round((y / this.image.height) * rect.height);

        marker.style.left = `${relativeX}px`;
        marker.style.top = `${relativeY}px`;
    }
}

export default Main;