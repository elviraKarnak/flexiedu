// cspell:ignore cnsg subgr cngc

import '../scss/dashboard.scss';
import Dashboard from './dashboard';

document.addEventListener('DOMContentLoaded', function() {
	var dashboard = new Dashboard();
	dashboard.tabs();
	dashboard.toggleCheckbox();
	// cspell:disable-next-line
	dashboard.searchfromList();
	dashboard.searchGroups();
	// cspell:disable-next-line
	dashboard.scrolToElement();
	dashboard.replaceContent(
		'.cnsg-btn',
		'.ldgr-sub-groups-content',
		'.ldgr-create-new-sg'
	);
	dashboard.replaceContent(
		'.edit-sub-group-button',
		'.ldgr-sub-groups-content',
		'.ldgr-create-new-sg'
	);
	//dashboard.replaceContent('.ldgr-edit-subgr', '.ldgr-sub-groups-content', '.ldgr-edit-sg');
	dashboard.replaceContent(
		'.ldgr-cngc-btn',
		'.ldgr-group-code-content',
		'.ldgr-group-code-create-section'
	);
	dashboard.replaceContent(
		'.ldgr-edit-code',
		'.ldgr-group-code-content',
		'.ldgr-group-code-edit-section'
	);
	dashboard.replaceContent(
		'.create-sg-cancel',
		'.ldgr-create-new-sg',
		'.ldgr-sub-groups-content'
	);
	dashboard.replaceContent(
		'.edit-sg-cancel',
		'.ldgr-edit-sg',
		'.ldgr-sub-groups-content'
	);
	dashboard.replaceContent(
		'.gcs-cancel',
		'.ldgr-group-code-setting',
		'.ldgr-group-code-content'
	);
	dashboard.openLightbox('.ldgr-edit-group', '#ldgr-edit-group-popup');
	dashboard.openLightbox('.enroll-new-user', '#ldgr-enroll-users-popup');
	dashboard.closeLightbox('.ldgr-icon-Close', '.ldgr-lightbox');
	dashboard.closeLightbox('.upload-csv-cancel', '#ldgr-enroll-users-popup');
	dashboard.closeLightbox('.add-usr-cancel', '#ldgr-enroll-users-popup');
	dashboard.closeLightbox('.edit-group-cancel', '#ldgr-edit-group-popup');
	dashboard.closePopupOutsideClick();
	dashboard.removeUsers();
	dashboard.addMoreUsers();
	dashboard.pagination();
});