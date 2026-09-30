import './bootstrap';

document.addEventListener('DOMContentLoaded', () => {
	const createModal = document.getElementById('createKilometerModal');
	const todayValue = () => {
		const today = new Date();
		const month = String(today.getMonth() + 1).padStart(2, '0');
		const day = String(today.getDate()).padStart(2, '0');

		return `${today.getFullYear()}-${month}-${day}`;
	};

	if (createModal) {
		const resetCreateKilometerForm = () => {
			const form = createModal.querySelector('form.kilometer-form');

			if (!form) {
				return;
			}

			form.reset();
			form.querySelectorAll('select').forEach((select) => {
				select.value = '';
			});
			form.querySelectorAll('input:not([type="hidden"]):not([name="date"]):not([name="ngay"])').forEach((input) => {
				input.value = '';
			});

			const dateInput = form.querySelector('input[name="date"], input[name="ngay"]');
			if (dateInput) {
				dateInput.value = todayValue();
			}
		};

		createModal.addEventListener('show.bs.modal', resetCreateKilometerForm);
		createModal.addEventListener('hidden.bs.modal', resetCreateKilometerForm);
	}

	const createEmployeeModal = document.getElementById('createEmployeeModal');

	if (createEmployeeModal) {
		const resetCreateEmployeeForm = () => {
			const form = createEmployeeModal.querySelector('form.employee-form');

			if (!form) {
				return;
			}

			form.reset();
			form.querySelector('input[name="name"]').value = '';
			form.querySelector('textarea[name="address"]').value = '';
			form.querySelector('input[name="started_at"]').value = todayValue();

			const thumbnailInput = form.querySelector('input[name="thumbnail"]');
			if (thumbnailInput) {
				thumbnailInput.value = '';
			}

			const preview = form.querySelector('.employee-form-preview');
			if (preview) {
				preview.src = preview.dataset.defaultThumbnail;
			}

			const supportCheckbox = form.querySelector('input[name="is_support"]');
			if (supportCheckbox) {
				supportCheckbox.checked = false;
			}
		};

		createEmployeeModal.addEventListener('show.bs.modal', resetCreateEmployeeForm);
		createEmployeeModal.addEventListener('hidden.bs.modal', resetCreateEmployeeForm);
	}

	const createCargoModal = document.getElementById('createCargoModal');

	if (createCargoModal) {
		const resetCreateCargoForm = () => {
			const form = createCargoModal.querySelector('form.cargo-delivery-form');

			if (!form) {
				return;
			}

			form.reset();

			const dateInput = form.querySelector('input[name="delivery_date"]');
			if (dateInput) {
				dateInput.value = todayValue();
				dateInput.disabled = false;
				dateInput.readOnly = false;
				dateInput.removeAttribute('disabled');
				dateInput.removeAttribute('readonly');
			}

			const employeeSelect = form.querySelector('select[name="employee_id"]');
			if (employeeSelect) {
				employeeSelect.value = '';
			}

			const tripCountInput = form.querySelector('input[name="trip_count"]');
			if (tripCountInput) {
				tripCountInput.value = '1';
			}

			const packageCountInput = form.querySelector('input[name="package_count"]');
			if (packageCountInput) {
				packageCountInput.value = '';
			}

			const weightInput = form.querySelector('input[name="weight_kg"]');
			if (weightInput) {
				weightInput.value = '';
			}

			const noteTextarea = form.querySelector('textarea[name="note"], textarea[name="package_note"]');
			if (noteTextarea) {
				noteTextarea.value = '';
			}

			form.querySelectorAll('input[name="group_id"]').forEach((radio) => {
				radio.checked = false;
			});

			form.querySelectorAll('.cargo-kilometer-option').forEach((option) => {
				option.hidden = false;
				option.classList.remove('is-hidden');
				option.style.removeProperty('display');
			});

			form.querySelectorAll('[data-cargo-kilometer-search]').forEach((input) => {
				input.value = '';
			});

			const emptyMsg = form.querySelector('.cargo-kilometer-empty');
			if (emptyMsg) {
				emptyMsg.style.display = 'none';
			}

			const alertDiv = form.querySelector('.cargo-form-alert');
			if (alertDiv) {
				alertDiv.style.display = 'none';
				alertDiv.innerHTML = '';
			}
		};

		createCargoModal.addEventListener('show.bs.modal', resetCreateCargoForm);
		createCargoModal.addEventListener('hidden.bs.modal', resetCreateCargoForm);

		document.querySelectorAll('[data-bs-target="#createCargoModal"]').forEach((btn) => {
			btn.addEventListener('click', resetCreateCargoForm);
		});

		const form = createCargoModal.querySelector('form.cargo-delivery-form');
		const alertDiv = form ? form.querySelector('.cargo-form-alert') : null;
		const submitBtn = form ? form.querySelector('button[type="submit"]') : null;

		if (form && !form.dataset.ajaxBound) {
			form.dataset.ajaxBound = 'true';
			form.addEventListener('submit', async (e) => {
				e.preventDefault();

				const selectedRadio = form.querySelector('input[name="group_id"]:checked');
				if (!selectedRadio) {
					if (alertDiv) {
						alertDiv.className = 'cargo-form-alert alert alert-danger';
						alertDiv.innerHTML = '⚠️ Vui lòng chọn một thông tin trong danh sách giao hàng bên dưới.';
						alertDiv.style.display = 'block';
					}
					const listWrap = form.querySelector('.cargo-kilometer-table-wrap');
					if (listWrap) {
						listWrap.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
					}
					return;
				}

				if (!form.reportValidity()) {
					return;
				}

				const originalBtnText = submitBtn ? submitBtn.textContent : 'Lưu thông tin';
				if (submitBtn) {
					submitBtn.disabled = true;
					submitBtn.textContent = 'Đang lưu...';
				}

				if (alertDiv) {
					alertDiv.style.display = 'none';
					alertDiv.innerHTML = '';
				}

				try {
					const formData = new FormData(form);
					const response = await fetch(form.action, {
						method: 'POST',
						body: formData,
						headers: {
							'X-Requested-With': 'XMLHttpRequest',
							'Accept': 'application/json',
							'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
						},
					});

					const result = await response.json();

					if (response.ok && result.success) {
						if (alertDiv) {
							alertDiv.className = 'cargo-form-alert alert alert-success';
							alertDiv.innerHTML = '✓ ' + (result.message || 'Đã lưu thông tin chở hàng thành công!') + ' Dữ liệu đã được thêm ngoài danh sách.';
							alertDiv.style.display = 'block';
						}

						const tbody = document.getElementById('cargo-deliveries-tbody') || document.querySelector('.cargo-table tbody');
						if (tbody && result.row_html) {
							const emptyRow = tbody.querySelector('.empty-table-row, .empty-table-cell');
							if (emptyRow) {
								const tr = emptyRow.closest('tr');
								if (tr) tr.remove();
							}
							tbody.insertAdjacentHTML('afterbegin', result.row_html);
							const newRow = tbody.querySelector('tr:first-child');
							if (newRow) {
								newRow.classList.add('row-newly-added');
							}
						}

						if (result.modal_html) {
							const dynamicModalsContainer = document.getElementById('dynamic-edit-modals') || document.body;
							dynamicModalsContainer.insertAdjacentHTML('beforeend', result.modal_html);
						}

						form.querySelectorAll('input[name="group_id"]').forEach((radio) => {
							radio.checked = false;
						});

						const packageCountInput = form.querySelector('input[name="package_count"]');
						if (packageCountInput) packageCountInput.value = '';

						const weightInput = form.querySelector('input[name="weight_kg"]');
						if (weightInput) weightInput.value = '';

						const noteTextarea = form.querySelector('textarea[name="note"]');
						if (noteTextarea) noteTextarea.value = '';

						form.querySelectorAll('[data-cargo-kilometer-search]').forEach((input) => {
							input.value = '';
						});

						form.querySelectorAll('.cargo-kilometer-option').forEach((option) => {
							option.hidden = false;
							option.classList.remove('is-hidden');
							option.style.removeProperty('display');
						});

						const emptyMsg = form.querySelector('.cargo-kilometer-empty');
						if (emptyMsg) emptyMsg.style.display = 'none';

					} else {
						let errorMsg = result.message || 'Có lỗi xảy ra khi lưu thông tin. Vui lòng kiểm tra lại.';
						if (result.errors) {
							const firstKey = Object.keys(result.errors)[0];
							if (firstKey && result.errors[firstKey][0]) {
								errorMsg = result.errors[firstKey][0];
							}
						}
						if (alertDiv) {
							alertDiv.className = 'cargo-form-alert alert alert-danger';
							alertDiv.innerHTML = '✕ ' + errorMsg;
							alertDiv.style.display = 'block';
						}
					}
				} catch (err) {
					console.error('Lỗi khi gửi form:', err);
					if (alertDiv) {
						alertDiv.className = 'cargo-form-alert alert alert-danger';
						alertDiv.innerHTML = '✕ Không thể kết nối đến máy chủ. Vui lòng thử lại.';
						alertDiv.style.display = 'block';
					}
				} finally {
					if (submitBtn) {
						submitBtn.disabled = false;
						submitBtn.textContent = originalBtnText;
					}
				}
			});
		}
	}

	const createGroupModal = document.getElementById('createGroupModal');

	if (createGroupModal) {
		const resetCreateGroupForm = () => {
			const form = createGroupModal.querySelector('form.group-form');

			if (!form) {
				return;
			}

			form.reset();
			form.querySelector('input[name="invoice_name"]').value = '';
			const packageNoteField = form.querySelector('textarea[name="package_note"]');
			if (packageNoteField) {
				packageNoteField.value = '';
			}

			const kilometerSelect = form.querySelector('select[name="kilometer_id"]');
			if (kilometerSelect) {
				kilometerSelect.value = '';
			}

			const kilometerInput = form.querySelector('[data-group-kilometer-input]');
			if (kilometerInput) {
				kilometerInput.value = '';
				kilometerInput.setAttribute('aria-expanded', 'false');
			}

			const optionsContainer = form.querySelector('[data-group-kilometer-options]');
			if (optionsContainer) {
				optionsContainer.hidden = true;
				optionsContainer.innerHTML = '';
			}
		};

		createGroupModal.addEventListener('show.bs.modal', resetCreateGroupForm);
		createGroupModal.addEventListener('hidden.bs.modal', resetCreateGroupForm);
	}

	const normalizeSearchText = (str) => {
		if (!str) return '';
		return str
			.toString()
			.toLowerCase()
			.normalize('NFD')
			.replace(/[\u0300-\u036f]/g, '')
			.replace(/đ/g, 'd')
			.replace(/Đ/g, 'd')
			.replace(/[^\w\s]/gi, ' ')
			.trim();
	};

	document.addEventListener('input', (event) => {
		const searchInput = event.target;
		if (!searchInput || !searchInput.matches('[data-cargo-kilometer-search]')) return;

		const form = searchInput.closest('.cargo-delivery-form');
		if (!form) return;

		const rawTerm = searchInput.value.trim();
		const options = form.querySelectorAll('.cargo-kilometer-option');
		const emptyMsg = form.querySelector('.cargo-kilometer-empty');

		if (!rawTerm) {
			options.forEach((option) => {
				option.hidden = false;
				option.classList.remove('is-hidden');
				option.style.removeProperty('display');
			});
			if (emptyMsg) emptyMsg.style.display = 'none';
			return;
		}

		const terms = normalizeSearchText(rawTerm).split(/\s+/).filter(Boolean);
		let visibleCount = 0;

		options.forEach((option) => {
			const optionSearch = normalizeSearchText(option.dataset.search || '');
			const matches = terms.every((t) => optionSearch.includes(t));
			option.hidden = !matches;
			if (matches) {
				option.classList.remove('is-hidden');
				option.style.removeProperty('display');
				visibleCount++;
			} else {
				option.classList.add('is-hidden');
				option.style.setProperty('display', 'none', 'important');
			}
		});

		if (emptyMsg) {
			emptyMsg.style.display = visibleCount === 0 ? 'block' : 'none';
		}
	});

	document.querySelectorAll('[data-group-kilometer-combobox]').forEach((combobox) => {
		const input = combobox.querySelector('[data-group-kilometer-input]');
		const select = combobox.querySelector('.group-kilometer-native-select');
		const optionsContainer = combobox.querySelector('[data-group-kilometer-options]');
		const options = Array.from(select.options).filter((option) => option.value);

		const renderOptions = (term = '') => {
			const matches = options.filter((option) => option.dataset.search.includes(term.toLowerCase()));
			optionsContainer.innerHTML = matches.map((option) => `<button type="button" class="group-kilometer-option" data-value="${option.value}">${option.dataset.label}</button>`).join('');
			optionsContainer.hidden = matches.length === 0;
		};

		const selectedOption = options.find((option) => option.selected);
		if (selectedOption) {
			input.value = selectedOption.dataset.label;
		}

		input.addEventListener('focus', () => {
			input.select();
			renderOptions(input.value);
			input.setAttribute('aria-expanded', 'true');
		});
		input.addEventListener('input', () => {
			select.value = '';
			renderOptions(input.value);
			input.setAttribute('aria-expanded', 'true');
		});
		optionsContainer.addEventListener('click', (event) => {
			const optionButton = event.target.closest('[data-value]');
			const option = options.find((item) => item.value === optionButton?.dataset.value);

			if (!option) {
				return;
			}

			select.value = option.value;
			input.value = option.dataset.label;
			optionsContainer.hidden = true;
			input.setAttribute('aria-expanded', 'false');
		});

		document.addEventListener('click', (event) => {
			if (!combobox.contains(event.target)) {
				optionsContainer.hidden = true;
				input.setAttribute('aria-expanded', 'false');
			}
		});
	});

	const permissionRole = document.querySelector('[data-permission-role]');
	const permissionGroup = document.querySelector('[data-permission-group]');
	if (permissionRole && permissionGroup) {
		const togglePermissionGroup = () => {
			const isGroup = permissionRole.value === 'group';
			permissionGroup.hidden = !isGroup;
			const targetField = permissionGroup.querySelector('input, select');
			if (targetField) {
				targetField.required = isGroup;
				if (!isGroup) {
					targetField.value = '';
				}
			}
		};
		permissionRole.addEventListener('change', togglePermissionGroup);
		togglePermissionGroup();
	}
});
