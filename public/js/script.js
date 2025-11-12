document.addEventListener('DOMContentLoaded', () => {
    const mobileMenuButton = document.getElementById('mobile-menu-button');
    const mobileMenu = document.getElementById('mobile-menu');

    const closeMobileMenu = () => {
        if (!mobileMenu) {
            return;
        }

        if (!mobileMenu.classList.contains('hidden')) {
            mobileMenu.classList.add('hidden');
        }

        mobileMenu.classList.remove('show');
    };

    if (mobileMenuButton && mobileMenu) {
        mobileMenuButton.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
            mobileMenu.classList.toggle('show');
        });
    }

    const plansSection = document.getElementById('precos');
    const planLinks = document.querySelectorAll('.assine-agora-link');

    const scrollToPlans = () => {
        if (!plansSection) {
            return;
        }

        const headerOffset = 90;
        const elementPosition = plansSection.getBoundingClientRect().top + window.scrollY;
        const offsetPosition = Math.max(elementPosition - headerOffset, 0);

        window.scrollTo({
            top: offsetPosition,
            behavior: 'smooth',
        });
    };

    planLinks.forEach((link) => {
        link.addEventListener('click', (event) => {
            event.preventDefault();
            scrollToPlans();
            closeMobileMenu();
        });
    });

    const modal = document.getElementById('plan-registration-modal');

    if (!modal) {
        return;
    }

    const planButtons = document.querySelectorAll('.plan-select-button');
    const planInput = modal.querySelector('#selected-plan-id');
    const planNameDisplay = modal.querySelector('#selected-plan-name');
    const planSummaryDisplay = modal.querySelector('#selected-plan-summary');
    const planTrialDisplay = modal.querySelector('#selected-plan-trial');
    const nameField = modal.querySelector('#registration-name');
    const overlay = modal.querySelector('[data-modal-overlay]');
    const closeButtons = modal.querySelectorAll('[data-modal-close]');

    const disableScroll = () => document.body.classList.add('overflow-hidden');
    const enableScroll = () => document.body.classList.remove('overflow-hidden');

    const isModalHidden = () => modal.classList.contains('hidden');

    const openModal = () => {
        modal.classList.remove('hidden');
        modal.setAttribute('aria-hidden', 'false');
        disableScroll();

        if (nameField) {
            window.setTimeout(() => nameField.focus(), 0);
        }
    };

    const closeModal = () => {
        modal.classList.add('hidden');
        modal.setAttribute('aria-hidden', 'true');
        enableScroll();
    };

    const updateTextContent = (element, value) => {
        if (!element) {
            return;
        }

        if (value) {
            element.textContent = value;
            element.classList.remove('hidden');
        } else {
            element.textContent = '';
            element.classList.add('hidden');
        }
    };

    planButtons.forEach((button) => {
        button.addEventListener('click', () => {
            const planId = button.dataset.planId || '';
            const planName = button.dataset.planName || 'Plano';
            const planSummary = button.dataset.planSummary || '';
            const planTrial = button.dataset.planTrial || '';

            if (planInput) {
                planInput.value = planId;
            }

            if (planNameDisplay) {
                planNameDisplay.textContent = planName;
            }

            updateTextContent(planSummaryDisplay, planSummary);
            updateTextContent(planTrialDisplay, planTrial);

            openModal();
        });
    });

    closeButtons.forEach((button) => {
        button.addEventListener('click', () => {
            closeModal();
        });
    });

    if (overlay) {
        overlay.addEventListener('click', () => {
            closeModal();
        });
    }

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && !isModalHidden()) {
            closeModal();
        }
    });

    const shouldOpen = modal.dataset.shouldOpen === 'true';
    if (shouldOpen) {
        openModal();
    }
});
