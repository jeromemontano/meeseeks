import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
    static targets = [
        "summonButton",
        "requestContainer",
        "resultsContainer",
        "formContainer",
        "taskSelect",
        "noRecords"
    ]

    summon() {
        this.summonButtonTarget.classList.add("hidden")
        this.requestContainerTarget.classList.remove("hidden")
        this.resultsContainerTarget.classList.add("hidden")
        this.formContainerTarget.classList.remove("hidden")
        this.formContainerTarget.reset();
        this.taskSelectTarget.dispatchEvent(new Event("change", { bubbles: true }));
    }

    requestFulfilled(event) {
        event.currentTarget.classList.add("hidden")
        this.summonButtonTarget.classList.remove("hidden")
        this.requestContainerTarget.classList.add("hidden")
        this.requestContainerTarget.classList.add("hidden")
        this.noRecordsTarget.classList.add("hidden")
    }
}
