(function () {
	'use strict';

	function closeModal(modal) {

		if (!modal) {
			return;
		}

		modal.classList.remove('is-active');
		modal.setAttribute('aria-hidden', 'true');

		if (!document.querySelector('.ecp-modal.is-active')) {
			document.body.style.overflow = '';
		}

	}

	function openModal(modal) {

		if (!modal) {
			return;
		}

		modal.classList.add('is-active');
		modal.setAttribute('aria-hidden', 'false');
		document.body.style.overflow = 'hidden';

	}

	document.addEventListener('click', function (event) {

		var trigger = event.target.closest('[data-ecp-modal]');

		if (trigger) {
			event.preventDefault();
			var targetId = trigger.getAttribute('data-ecp-modal');
			var modal = document.getElementById(targetId);
			if (modal) {
				openModal(modal);
			}
			return;
		}

		var closeBtn = event.target.closest('[data-ecp-close]');

		if (closeBtn) {
			event.preventDefault();
			var activeModal = closeBtn.closest('.ecp-modal');
			if (activeModal) {
				closeModal(activeModal);
			}
		}

	});

	document.addEventListener('keydown', function (event) {

		if (event.key === 'Escape' || event.keyCode === 27) {
			var activeModals = document.querySelectorAll('.ecp-modal.is-active');
			activeModals.forEach(function (modal) {
				closeModal(modal);
			});
		}

	});

})();
