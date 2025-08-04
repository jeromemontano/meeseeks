import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = [
        "characterProfile",
        "profileModal",
        "detailContent",
        "profileImage",
        "profileName",
        "profileDetailList",
    ];

    connect() {
        this.handleClickOutside = this.handleClickOutsideModal.bind(this);
        this._justOpened = false;
    }

    disconnect() {
        document.removeEventListener("click", this._handleClickOutside);
    }

    showDetail(event) {
        const card = event.currentTarget;
        const raw = card.dataset.details;
        if (!raw) return;

        let character;
        try {
            character = JSON.parse(raw);
        } catch {
            return;
        }

        const image = document.createElement("img");
        image.src = character.image
        image.alt = character.name
        this.profileImageTarget.innerHTML = ""
        this.profileImageTarget.appendChild(image)

        const name = document.createElement("h2")
        name.textContent = character.name
        this.profileDetailListTarget.innerHTML = ""
        this.profileDetailListTarget.appendChild(name);
        this.profileDetailListTarget.appendChild(this.createProfileDetailElement("Status: ", character.status))
        this.profileDetailListTarget.appendChild(this.createProfileDetailElement("Species: ", character.species))
        this.profileDetailListTarget.appendChild(this.createProfileDetailElement("Gender: ", character.gender))
        this.profileDetailListTarget.appendChild(this.createProfileDetailElement("Origin: ", character.origin?.name || "unknown"))
        this.profileDetailListTarget.appendChild(this.createProfileDetailElement("Location: ", character.location?.name || "unknown"))
        this.profileDetailListTarget.appendChild(this.createEpisodeElement(character));

        this.characterProfileTarget.classList.remove("hidden");

        requestAnimationFrame(() => {
            document.addEventListener("click", this.handleClickOutside);
        });

        this._justOpened = true;
        setTimeout(() => {
            this._justOpened = false;
        }, 50);

        setTimeout(() => {
            document.addEventListener("click", this._handleClickOutside);
        }, 0);
    }

    createProfileDetailElement(detailName, detailValue) {
        const detailLine = document.createElement("p")
        const detail = document.createElement("span")
        const value = document.createElement("span")

        detail.textContent = detailName
        value.textContent = detailValue

        detailLine.appendChild(detail)
        detailLine.appendChild(value)
        return detailLine
    }

    createEpisodeElement(character) {
        const episodesContainer = document.createElement("div");
        episodesContainer.classList.add("mt-3");

        const label = document.createElement("span");
        label.classList.add("font-semibold");
        label.textContent = "Episodes:";
        episodesContainer.appendChild(label);

        const badgesWrapper = document.createElement("div");
        badgesWrapper.classList.add("flex", "flex-wrap", "gap-2", "mt-1");

        character.episode.slice(0, 10).forEach((ep) => {
            const badge = document.createElement("span");
            badge.classList.add("bg-cyan-700", "px-2", "py-1", "rounded", "text-xs");
            badge.textContent = ep;
            badgesWrapper.appendChild(badge);
        });

        if (character.episode.length > 10) {
            const moreBadge = document.createElement("span");
            moreBadge.classList.add("text-sm", "opacity-70");
            moreBadge.textContent = `+${character.episode.length - 10} more`;
            badgesWrapper.appendChild(moreBadge);
        }

        episodesContainer.appendChild(badgesWrapper);

        return episodesContainer;
    }

    closeDetail() {
        this.characterProfileTarget.classList.add("hidden");
        this.profileDetailListTarget.innerHTML = "";
        document.removeEventListener("click", this._handleClickOutside);
    }

    handleClickOutsideModal(event) {
        if (this._justOpened) return;
        if (!this.profileModalTarget.contains(event.target)) {
            this.closeDetail()
        }
    }
}
