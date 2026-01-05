// ページ読み込み時の初期化処理
document.addEventListener('DOMContentLoaded', function() {
    // 各機能の初期化
    FacilityManager.accordion.initialize();
    FacilityManager.equipment.initialize();
    FacilityManager.validation.initialize();
    FacilityManager.category.initialize();
    FacilityManager.deleteOptions.initialize();
    FacilityManager.rowSelection.initialize();

    //itemのモーダル
    FacilityManager.items.initialize();

    // アコーディオンの初期状態を確認
    FacilityManager.accordion.update();

    // 入力フィールドの変更時にアコーディオンを更新
    const inputFields = document.querySelectorAll('#room_name, #category_name, #max_number_of_people, #equipment, #time_of_unit_price, #rental_unit_price');
    inputFields.forEach(field => {
        field.addEventListener('input', () => FacilityManager.accordion.update());
    });

    // グローバル関数の設定
    window.submitAction = function(action) {
        const selectedFacility = document.querySelector("input[name='selected_room']:checked");
        
        if (!selectedFacility) {
            alert(`${action === 'change' ? '変更' : '削除'}する施設を選択してください。`);
            return;
        }

        const roomNumber = selectedFacility.value;
        const categoryNumber = selectedFacility.getAttribute('data-category-number');

        const actions = {
            'add': '../facility_edit/facility_add.php',
            'change': `facility_change.php?room_number=${roomNumber}&category_number=${categoryNumber}`,
            'delete': `facility_delete.php?room_number=${roomNumber}&category_number=${categoryNumber}`
        };

        if (actions[action]) {
            window.location.href = actions[action];
        }
    };

    // カテゴリー関連のグローバル関数の設定
    window.updateCategories = () => FacilityManager.category.updateCategories();
    window.deleteCategories = () => FacilityManager.category.deleteCategories();

    // 備品管理用のグローバル関数の設定
    window.updateItems = () => FacilityManager.items.updateItems();
    window.deleteItems = () => FacilityManager.items.deleteItems();
});



// モジュールとしての構造化
const FacilityManager = {
    // 共通ユーティリティ
    utils: {
        createHiddenInput(name, value) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = name;
            input.value = value;
            return input;
        },
        
        showError(message) {
            alert(message);
        },
        
        confirmAction(message) {
            return confirm(message);
        }
    },

    // アコーディオン管理
    accordion: {
        initialize() {
            // アコーディオンの初期化
            const accordionHeaders = document.querySelectorAll('.accordion-header');
            accordionHeaders.forEach(header => {
                header.addEventListener('click', () => {
                    const content = header.nextElementSibling;
                    const icon = header.querySelector('.icon');
                    content.classList.toggle('active');
                    icon.classList.toggle('rotate');
                });
            });
        },

        update() {
            // 部屋名と最大収容人数の入力状態を確認
            const roomName = document.getElementById('room_name');
            const maxNumberOfPeople = document.getElementById('max_number_of_people');

            const basicFilled = 
                (roomName && roomName.value.trim() !== '') &&
                (maxNumberOfPeople && maxNumberOfPeople.value.trim() !== '');
            
            // 設備と料金の入力状態を確認
            const equipmentInput = document.getElementById('equipment');
            const equipmentFilled = equipmentInput && equipmentInput.value.trim() !== '';

            const timeOfUnitPriceInput = document.getElementById('time_of_unit_price');
            const rentalUnitPriceInput = document.getElementById('rental_unit_price');
            const feeFilled = 
                (timeOfUnitPriceInput && timeOfUnitPriceInput.value.trim() !== '') || 
                (rentalUnitPriceInput && rentalUnitPriceInput.value.trim() !== '');

            // 設備のアコーディオン制御
            const equipmentAccordion = document.getElementById('equipment_accordion');
            if (equipmentAccordion) {
                const equipmentContent = equipmentAccordion.querySelector('.accordion-content');
                const equipmentIcon = equipmentAccordion.querySelector('.icon');
                
                if (basicFilled || equipmentFilled) {
                    equipmentContent.classList.add('active');
                    equipmentIcon.classList.add('rotate');
                } else {
                    equipmentContent.classList.remove('active');
                    equipmentIcon.classList.remove('rotate');
                }
            }

            // 料金設定のアコーディオン制御
            const feeAccordion = document.getElementById('fee_accordion');
            if (feeAccordion) {
                const feeContent = feeAccordion.querySelector('.accordion-content');
                const feeIcon = feeAccordion.querySelector('.icon');
                
                if (basicFilled || feeFilled) {
                    feeContent.classList.add('active');
                    feeIcon.classList.add('rotate');
                } else {
                    feeContent.classList.remove('active');
                    feeIcon.classList.remove('rotate');
                }
            }
        }
    },

    // 備品管理
    equipment: {
        initialize() {
            const referenceButton = document.getElementById('reference_button');
            const itemTable = document.querySelector('#item_change_form table');
            
            if (referenceButton && itemTable) {
                referenceButton.addEventListener('click', function() {
                    const selectedRadio = itemTable.querySelector('input[name="selected_item"]:checked');
                    
                    if (selectedRadio) {
                        const row = selectedRadio.closest('tr');
                        const cells = row.cells;
                        
                        const item_number = cells[1].textContent.trim();
                        const item_name = cells[2].textContent.trim();
                        const total_of_item = cells[3].textContent.trim().replace('個', '');
                        
                        let rental_unit_price = '';
                        if (cells.length > 4) {
                            rental_unit_price = cells[4].textContent.trim().replace('円', '');
                        }
                        
                        document.getElementById('item_name').value = item_name;
                        document.getElementById('total_of_item').value = total_of_item;
                        
                        const rental_unit_price_input = document.getElementById('rental_unit_price');
                        if (rental_unit_price_input && rental_unit_price !== '') {
                            rental_unit_price_input.value = rental_unit_price;
                        }
                        
                        document.getElementById('item_number_for_js').value = item_number;
                    } else {
                        alert('備品を選択してください。');
                    }
                });
            }
        }
    },

    // フォームバリデーション 02/10
    validation: {
        initialize() {
            // フォーム要素の取得
            const addForm = document.getElementById('facility_add');
            const changeForm = document.getElementById('facility_change');
            const deleteForm = document.querySelector('form[action="facility_delete_check.php"]');
    
            // 各フォームのバリデーション設定
            if (addForm) {
                addForm.addEventListener('submit', function(e) {
                    if (!FacilityManager.validation.validateAddForm(this)) {
                        e.preventDefault();
                    }
                });
            }
    
            if (changeForm) {
                const initialValues = FacilityManager.validation.getInitialValues();
                changeForm.addEventListener('submit', function(e) {
                    if (!FacilityManager.validation.validateChangeForm(this, initialValues)) {
                        e.preventDefault();
                    }
                });
            }
    
            if (deleteForm) {
                deleteForm.addEventListener('submit', function(e) {
                    if (!FacilityManager.validation.validateDeleteForm(this)) {
                        e.preventDefault();
                    }
                });
            }
    
            // 入力フィールドの変更時にエラーメッセージを消す
            document.querySelectorAll('input, select').forEach(element => {
                element.addEventListener('input', function() {
                    FacilityManager.validation.removeErrorMessage(this);
                });
            });
        },
    
        showError(element, message) {
            this.removeErrorMessage(element);
            const errorDiv = document.createElement('div');
            errorDiv.className = 'error-message';
            errorDiv.textContent = message;
            element.parentNode.insertBefore(errorDiv, element.nextSibling);
            element.classList.add('error-input');
        },
    
        removeErrorMessage(element) {
            const existingError = element.parentNode.querySelector('.error-message');
            if (existingError) {
                existingError.remove();
            }
            element.classList.remove('error-input');
        },
    
        removeAllErrorMessages() {
            document.querySelectorAll('.error-message').forEach(error => error.remove());
            document.querySelectorAll('.error-input').forEach(input => input.classList.remove('error-input'));
        },
    
        validateAddForm(form) {
            this.removeAllErrorMessages();
            let hasError = false;
    
            // 入力フィールドの取得
            const fields = {
                room_name: form.querySelector('#room_name'),
                category_name: form.querySelector('#category_name'),
                max_number_of_people: form.querySelector('#max_number_of_people'),
                item_name: form.querySelector('#item_name'),
                total_of_item: form.querySelector('#total_of_item'),
                equipment: form.querySelector('#equipment'),
                time_of_unit_price: form.querySelector('#time_of_unit_price'),
                rental_unit_price: form.querySelector('#rental_unit_price')
            };
    
            // すべての入力が空の場合、全項目にエラー表示
            const allEmpty = Object.values(fields).every(field => 
                !field || !field.value.trim()
            );
    
            if (allEmpty) {
                Object.entries(fields).forEach(([key, field]) => {
                    if (field) {
                        this.showError(field, `${field.labels[0]?.textContent || key}を入力してください。`);
                    }
                });
                return false;
            }
    
            // 部屋名と最大収容人数の依存関係チェック
            if (fields.room_name?.value.trim()) {
                if (!fields.max_number_of_people?.value.trim()) {
                    this.showError(fields.max_number_of_people, '最大収容人数を入力してください。');
                    hasError = true;
                }
            }
    
            // 備品名と備品総数の依存関係チェック
            if (fields.item_name?.value.trim()) {
                if (!fields.total_of_item?.value.trim()) {
                    this.showError(fields.total_of_item, '備品総数を入力してください。');
                    hasError = true;
                }
            }
    
            // 部屋名依存の項目チェック
            if (!fields.room_name?.value.trim()) {
                if (fields.category_name?.value.trim()) {
                    this.showError(fields.room_name, '分類名を設定するには部屋名の入力が必要です。');
                    hasError = true;
                }
                if (fields.time_of_unit_price?.value.trim()) {
                    this.showError(fields.room_name, '時間単位当たりの料金を設定するには部屋名の入力が必要です。');
                    hasError = true;
                }
                if (fields.equipment?.value.trim()) {
                    this.showError(fields.room_name, '設備を設定するには部屋名の入力が必要です。');
                    hasError = true;
                }
            }
    
            // 備品名依存の項目チェック
            if (fields.rental_unit_price?.value.trim() && !fields.item_name?.value.trim()) {
                this.showError(fields.item_name, '貸出単価を設定するには備品名の入力が必要です。');
                hasError = true;
            }
    
            // 数値フィールドのバリデーション
            const numberFields = {
                'max_number_of_people': { min: 1, message: '最大収容人数は1以上の数値を入力してください。' },
                'total_of_item': { min: 1, message: '備品総数は1以上の数値を入力してください。' },
                'time_of_unit_price': { min: 0, message: '時間単位あたりの料金は0以上の数値を入力してください。' },
                'rental_unit_price': { min: 0, message: '貸出単価は0以上の数値を入力してください。' }
            };
    
            for (const [fieldId, config] of Object.entries(numberFields)) {
                const field = fields[fieldId];
                if (field && field.value.trim() !== '' && 
                    (isNaN(field.value) || parseInt(field.value) < config.min)) {
                    this.showError(field, config.message);
                    hasError = true;
                }
            }
    
            return !hasError;
        },
    
        validateChangeForm(form, initialValues) {
            this.removeAllErrorMessages();
            let hasError = false;
    
            // 現在の値を取得
            const currentValues = {
                room_name: form.querySelector('#room_name')?.value || '',
                category_name: form.querySelector('#category_name')?.value || '',
                max_number_of_people: form.querySelector('#max_number_of_people')?.value || '',
                item_name: form.querySelector('#item_name')?.value || '',
                total_of_item: form.querySelector('#total_of_item')?.value || '',
                equipment: form.querySelector('#equipment')?.value || '',
                time_of_unit_price: form.querySelector('#time_of_unit_price')?.value || '',
                rental_unit_price: form.querySelector('#rental_unit_price')?.value || ''
            };
    
            // 値の変更をチェック
            let hasChanges = false;
            for (const key in initialValues) {
                if (initialValues[key] !== currentValues[key]) {
                    hasChanges = true;
                    break;
                }
            }
    
            // 変更がない場合のみエラーを表示
            if (!hasChanges) {
                for (const key in initialValues) {
                    const field = form.querySelector(`#${key}`);
                    if (field) {
                        this.showError(field, `${field.labels[0]?.textContent || key}が変更されていません。`);
                        hasError = true;
                    }
                }
            }
    
            // 数値フィールドのバリデーション
            if (hasChanges) {
                const numberValidations = {
                    'max_number_of_people': { min: 1, message: '最大収容人数は1以上の数値を入力してください。' },
                    'total_of_item': { min: 1, message: '備品総数は1以上の数値を入力してください。' },
                    'time_of_unit_price': { min: 0, message: '時間単位あたりの料金は0以上の数値を入力してください。' },
                    'rental_unit_price': { min: 0, message: '貸出単価は0以上の数値を入力してください。' }
                };
    
                for (const [fieldId, validation] of Object.entries(numberValidations)) {
                    const field = form.querySelector(`#${fieldId}`);
                    if (field && field.value !== '' && 
                        (isNaN(field.value) || parseInt(field.value) < validation.min)) {
                        this.showError(field, validation.message);
                        hasError = true;
                    }
                }
    
                // 依存関係のチェック
                if (currentValues.room_name && !currentValues.max_number_of_people) {
                    this.showError(form.querySelector('#max_number_of_people'), '最大収容人数を入力してください。');
                    hasError = true;
                }
    
                if (currentValues.item_name && !currentValues.total_of_item) {
                    this.showError(form.querySelector('#total_of_item'), '備品総数を入力してください。');
                    hasError = true;
                }
            }
    
            return !hasError;
        },
    
        validateDeleteForm(form) {
            this.removeAllErrorMessages();
            let hasError = false;
    
            const deleteTypeRadios = form.querySelectorAll('input[name="delete_type"]');
            const selectedDeleteType = Array.from(deleteTypeRadios).find(radio => radio.checked);
            const checkboxGroup = form.querySelector('.checkbox-group');
            const itemCheckboxes = form.querySelectorAll('input[name="delete_items_list[]"]');
            const isAnyItemChecked = Array.from(itemCheckboxes).some(checkbox => checkbox.checked);
    
            // 削除方法が選択されていない場合のエラー表示
            if (!selectedDeleteType && !isAnyItemChecked) {
                const deleteSection = form.querySelector('.delete');
                if (deleteSection) {
                    this.showError(deleteSection, '削除するものを選択してください。');
                    hasError = true;
                }
            }
    
            // 「削除する項目を選ぶ」が選択された場合のチェック
            if (selectedDeleteType && selectedDeleteType.value === 'partial') {
                const checkboxes = form.querySelectorAll('.checkbox-group input[type="checkbox"]');
                const isAnyChecked = Array.from(checkboxes).some(checkbox => checkbox.checked);
    
                if (!isAnyChecked && !isAnyItemChecked) {
                    if (checkboxGroup) {
                        this.showError(checkboxGroup, '削除する項目を1つ以上選択してください。');
                        hasError = true;
                    }
                }
            }
    
            // 備品の削除チェック
            const itemsSection = form.querySelector('.items-section');
            if (itemsSection && !selectedDeleteType && !isAnyItemChecked) {
                this.showError(itemsSection, '削除する備品を選択してください。');
                hasError = true;
            }
    
            return !hasError;
        },
    
        getInitialValues() {
            return {
                room_name: document.getElementById('room_name')?.value || '',
                category_name: document.getElementById('category_name')?.value || '',
                max_number_of_people: document.getElementById('max_number_of_people')?.value || '',
                item_name: document.getElementById('item_name')?.value || '',
                total_of_item: document.getElementById('total_of_item')?.value || '',
                equipment: document.getElementById('equipment')?.value || '',
                time_of_unit_price: document.getElementById('time_of_unit_price')?.value || '',
                rental_unit_price: document.getElementById('rental_unit_price')?.value || ''
            };
        }
    },

    // カテゴリー管理
    category: {
        initialize() {
            this.initializePagination();
            this.initializeModalEvents();
        },

        initializeModalEvents() {
            // モーダル制御
            window.openModal = () => {
                document.getElementById('categoryModal').style.display = 'block';
            };

            window.closeModal = () => {
                document.getElementById('categoryModal').style.display = 'none';
            };

            // モーダル外クリック処理を addEventListener に変更
            document.addEventListener('click', function(event) {
                const categoryModal = document.getElementById('categoryModal');
                const itemModal = document.getElementById('itemModal');
                
                if (event.target === categoryModal) {
                    window.closeModal();
                }
                if (event.target === itemModal) {
                    window.closeItemModal();
                }
            });

            window.toggleSelectAll = () => {
                const selectAllCheckbox = document.getElementById('selectAll');
                const checkboxes = document.querySelectorAll('.category-checkbox');
                checkboxes.forEach(checkbox => {
                    checkbox.checked = selectAllCheckbox.checked;
                });
            };
        },

        initializePagination() {
            const modal = document.getElementById('categoryModal');
            if (!modal) return;

            // モーダル内のクリックイベントを監視
            modal.addEventListener('click', function(e) {
                const pageLink = e.target.closest('.modal-pagination a');
                if (!pageLink) return; // ページネーションリンク以外のクリックは無視
                
                e.preventDefault();
                
                // クリックされたリンクのURLからパラメータを取得
                const url = new URL(pageLink.href);
                const page = url.searchParams.get('category_page');
                
                // 現在のURLパラメータを維持しながら、ページ番号を更新
                const currentUrl = new URL(window.location.href);
                const params = new URLSearchParams(currentUrl.search);
                params.set('category_page', page);

                // Ajaxリクエストを実行
                fetch(`${window.location.pathname}?${params.toString()}`)
                    .then(response => response.text())
                    .then(html => {
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');

                        // カテゴリーテーブルの内容を更新
                        const newTableBody = doc.querySelector('#categoryTableBody');
                        const currentTableBody = modal.querySelector('#categoryTableBody');
                        if (newTableBody && currentTableBody) {
                            currentTableBody.innerHTML = newTableBody.innerHTML;
                        }

                        // ページネーションの更新
                        const newPagination = doc.querySelector('.modal-pagination');
                        const currentPagination = modal.querySelector('.modal-pagination');
                        if (newPagination && currentPagination) {
                            currentPagination.innerHTML = newPagination.innerHTML;
                        }

                        // URLを更新（ブラウザの履歴を変更せずに）
                        window.history.replaceState(null, '', `?${params.toString()}`);

                        // チェックボックスの状態を初期化
                        document.getElementById('selectAll').checked = false;
                    })
                    .catch(error => {
                        console.error('ページ取得エラー:', error);
                        alert('ページの更新中にエラーが発生しました。');
                    });
            });
        },

        // カテゴリー更新処理
        updateCategories() {
            const selectedCheckboxes = document.querySelectorAll('.category-checkbox:checked');
            if (selectedCheckboxes.length === 0) {
                alert('更新するカテゴリーを選択してください。');
                return;
            }

            if (!confirm('選択したカテゴリーを更新しますか？')) {
                return;
            }

            const updateData = Array.from(selectedCheckboxes).map(checkbox => {
                const row = checkbox.closest('tr');
                return {
                    category_number: checkbox.getAttribute('data-category-number'),
                    new_name: row.querySelector('.category-name-input').value
                };
            });

            const form = document.createElement('form');
            form.method = 'POST';
            
            const currentUrl = new URL(window.location.href);
            form.action = `${currentUrl.pathname}${currentUrl.search}`;

            form.appendChild(this.createHiddenInput('update_categories', 'true'));
            updateData.forEach((data, index) => {
                form.appendChild(this.createHiddenInput(`category_numbers[]`, data.category_number));
                form.appendChild(this.createHiddenInput(`new_names[]`, data.new_name));
            });

            document.body.appendChild(form);
            form.submit();
        },

        // カテゴリー削除処理
        deleteCategories() {
            const selectedCheckboxes = document.querySelectorAll('.category-checkbox:checked');
            if (selectedCheckboxes.length === 0) {
                alert('削除するカテゴリーを選択してください。');
                return;
            }

            if (!confirm('選択したカテゴリーを削除しますか？')) {
                return;
            }

            const categoryNumbers = Array.from(selectedCheckboxes).map(checkbox => 
                checkbox.getAttribute('data-category-number')
            );

            const form = document.createElement('form');
            form.method = 'POST';
            
            const currentUrl = new URL(window.location.href);
            form.action = `${currentUrl.pathname}${currentUrl.search}`;

            form.appendChild(this.createHiddenInput('delete_categories', 'true'));
            categoryNumbers.forEach(number => {
                form.appendChild(this.createHiddenInput('category_numbers[]', number));
            });

            document.body.appendChild(form);
            form.submit();
        },

        createHiddenInput(name, value) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = name;
            input.value = value;
            return input;
        }
    },

    // 削除オプション管理
    deleteOptions: {
        initialize() {
            const deleteTypeRadios = document.querySelectorAll('input[name="delete_type"]');
            const deleteOptions = document.querySelector('.delete-options');
            const deleteForm = document.querySelector('form');

            if (deleteOptions) {
                deleteOptions.style.display = 'none';
                
                deleteTypeRadios.forEach(radio => {
                    radio.addEventListener('change', function() {
                        const isFullDelete = this.value === 'facility';
                        
                        deleteOptions.style.display = this.value === 'partial' ? 'block' : 'none';
                        if (this.value !== 'partial') {
                            const checkboxes = deleteOptions.querySelectorAll('input[type="checkbox"]');
                            checkboxes.forEach(checkbox => checkbox.checked = false);
                        }

                        FacilityManager.deleteOptions.manageHiddenInput(isFullDelete, deleteForm);
                    });
                });
            }
        },

        manageHiddenInput(isFullDelete, form) {
            const existingHidden = document.getElementById('delete_room');
            if (existingHidden) {
                existingHidden.remove();
            }

            if (isFullDelete) {
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.id = 'delete_room';
                hiddenInput.name = 'delete_items[]';
                hiddenInput.value = 'facility';
                form.appendChild(hiddenInput);
            }
        }
    },

    // 行選択管理
    rowSelection: {
        initialize() {
            this.initializeTableRowSelection();
            this.initializeCategoryRowSelection();
            this.initializeItemRowSelection();
        },

        initializeTableRowSelection() {
            const tbody = document.querySelector('table tbody');
            
            if (tbody) {
                tbody.addEventListener('click', function(e) {
                    const tr = e.target.closest('tr');
                    if (tr) {
                        const radio = tr.querySelector('input[type="radio"]');
                        if (radio) {
                            radio.checked = true;
                        }
                    }
                });
            }
        },

        initializeCategoryRowSelection() {
            const categoryTableBody = document.getElementById('categoryTableBody');
            
            if (categoryTableBody) {
                categoryTableBody.addEventListener('click', function(e) {
                    const tr = e.target.closest('tr');
                    if (tr) {
                        if (e.target.type === 'checkbox') return;
                        
                        const checkbox = tr.querySelector('.category-checkbox');
                        if (checkbox) {
                            checkbox.checked = !checkbox.checked;
                        }
                    }
                });
            }
        },

        initializeItemRowSelection() {
            const itemTableBody = document.getElementById('itemTableBody');
            
            if (itemTableBody) {
                itemTableBody.addEventListener('click', function(e) {
                    const tr = e.target.closest('tr');
                    if (tr) {
                        if (e.target.type === 'checkbox') return;
                        
                        const checkbox = tr.querySelector('input[type="checkbox"]');
                        if (checkbox) {
                            checkbox.checked = !checkbox.checked;
                        }
                    }
                });
            }
        }
    },
    // 備品管理
    items: {
        initialize() {
            this.initializePagination();
            this.initializeModalEvents();
            this.setupFormSubmission();
            this.setupAddItemForm();
            this.setupDebugLogging();  // デバッグログ設定を追加
        },

        setupDebugLogging() {
            // フォーム送信をデバッグするためのイベントリスナー
            document.addEventListener('submit', (e) => {
                if (e.target.closest('#itemModal')) {
                    console.log('フォーム送信イベント検知:', {
                        formData: new FormData(e.target),
                        targetForm: e.target
                    });
                }
            });
        },

        setupFormSubmission() {
            const addItemForm = document.querySelector('.add-item-form');
            if (addItemForm) {
                addItemForm.addEventListener('submit', (e) => {
                    e.preventDefault();  // デフォルトの送信を防止
                    
                    // フォームデータの収集
                    const formData = new FormData(addItemForm);
                    
                    // 必須項目のチェック
                    const itemName = formData.get('new_item_name');
                    const itemTotal = formData.get('new_item_total');
                    const itemPrice = formData.get('new_item_price');
                    
                    if (!itemName || !itemTotal || !itemPrice) {
                        alert('すべての項目を入力してください。');
                        return;
                    }
                    
                    // データ送信
                    fetch(window.location.href, {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.text();
                    })
                    .then(() => {
                        window.location.reload();
                    })
                    .catch(error => {
                        console.error('追加処理でエラーが発生:', error);
                        alert('追加処理中にエラーが発生しました。');
                    });
                });
            }
        },

        setupAddItemForm() {
            const addItemForm = document.querySelector('.add-item-form');
            if (addItemForm) {
                addItemForm.addEventListener('submit', (e) => {
                    e.preventDefault();
                    console.log('フォーム送信開始');
    
                    const formData = new FormData(addItemForm);
                    
                    // デバッグ用にフォームデータの内容を確認
                    for (let pair of formData.entries()) {
                        console.log(pair[0] + ': ' + pair[1]);
                    }
    
                    // add_itemパラメータを明示的に追加
                    formData.append('add_item', 'true');
    
                    fetch(window.location.href, {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => {
                        console.log('レスポンスステータス:', response.status);
                        return response.text();
                    })
                    .then(text => {
                        console.log('サーバーレスポンス:', text);
                        window.location.reload();
                    })
                    .catch(error => {
                        console.error('エラー:', error);
                        alert('エラーが発生しました: ' + error.message);
                    });
                });
            }
        },
    
        initializeModalEvents() {
            // モーダル制御
            window.openItemModal = () => {
                document.getElementById('itemModal').style.display = 'block';
            };
    
            window.closeItemModal = () => {
                document.getElementById('itemModal').style.display = 'none';
            };

            // モーダル外クリック処理を addEventListener に変更
            document.addEventListener('click', function(event) {
                const itemModal = document.getElementById('itemModal');
                if (event.target === itemModal) {
                    window.closeItemModal();
                }
            });
    
            window.toggleSelectAllItems = () => {
                    const selectAllCheckbox = document.getElementById('selectAllItems');
                    const checkboxes = document.querySelectorAll('.item-checkbox');
                    checkboxes.forEach(checkbox => {
                        checkbox.checked = selectAllCheckbox.checked;
                    });
                };

            // モーダル内のフォームのデフォルト送信を防ぐ
            const itemModal = document.getElementById('itemModal');
            if (itemModal) {
                const forms = itemModal.querySelectorAll('form');
                forms.forEach(form => {
                    form.addEventListener('submit', (e) => {
                        e.preventDefault();
                    });
                });
            }
            },

        initializePagination() {
            const modal = document.getElementById('itemModal');
            if (!modal) return;
        
            // モーダル内のクリックイベントを監視
            modal.addEventListener('click', function(e) {
                const pageLink = e.target.closest('.item-modal-pagination a');  // クラス名を変更
                if (!pageLink) return;
                
                e.preventDefault();
                
                const url = new URL(pageLink.href);
                const page = url.searchParams.get('item_page');
                
                const currentUrl = new URL(window.location.href);
                const params = new URLSearchParams(currentUrl.search);
                
                // category_pageパラメータを保持
                if (params.has('category_page')) {
                    const categoryPage = params.get('category_page');
                    params.set('category_page', categoryPage);
                }
                params.set('item_page', page);
        
                fetch(`${window.location.pathname}?${params.toString()}`)
                    .then(response => response.text())
                    .then(html => {
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');
        
                        // テーブル本体の更新
                        const newTableBody = doc.querySelector('#itemTableBody');
                        const currentTableBody = modal.querySelector('#itemTableBody');
                        if (newTableBody && currentTableBody) {
                            currentTableBody.innerHTML = newTableBody.innerHTML;
                        }
        
                        // ページネーションの更新
                        const newPagination = doc.querySelector('.item-modal-pagination');  // クラス名を変更
                        const currentPagination = modal.querySelector('.item-modal-pagination');  // クラス名を変更
                        if (newPagination && currentPagination) {
                            currentPagination.innerHTML = newPagination.innerHTML;
                        }
        
                        // URLを更新
                        window.history.replaceState(null, '', `?${params.toString()}`);
        
                        // チェックボックスをリセット
                        document.getElementById('selectAllItems').checked = false;
                    })
                    .catch(error => {
                        console.error('ページ取得エラー:', error);
                        alert('ページの更新中にエラーが発生しました。');
                    });
            });
        },

        updateItems() {
            const selectedCheckboxes = document.querySelectorAll('.item-checkbox:checked');
            if (selectedCheckboxes.length === 0) {
                alert('更新する備品を選択してください。');
                return;
            }
    
            if (!confirm('選択した備品を更新しますか？')) {
                return;
            }
    
            // デバッグ用のログ出力
            console.log('選択された備品:', selectedCheckboxes);
    
            const formData = new FormData();
            formData.append('update_items', 'true');
    
            const updateData = Array.from(selectedCheckboxes).map(checkbox => {
                const row = checkbox.closest('tr');
                const itemData = {
                    item_number: checkbox.getAttribute('data-item-number'),
                    item_name: row.querySelector('.item-name-input').value,
                    item_total: row.querySelector('.item-total-input').value,
                    item_price: row.querySelector('.item-price-input').value
                };
                
                // デバッグログ
                console.log('処理する備品データ:', itemData);
                
                return itemData;
            });
    
            updateData.forEach((data) => {
                formData.append('item_numbers[]', data.item_number);
                formData.append('item_names[]', data.item_name);
                formData.append('item_totals[]', data.item_total);
                formData.append('item_prices[]', data.item_price);
            });
    
            // Fetch APIを使用してPOSTリクエストを送信
            fetch(window.location.href, {
                method: 'POST',
                body: formData
            })
            .then(response => {
                console.log('サーバーレスポンス:', response);
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.text();
            })
            .then(() => {
                window.location.reload();
            })
            .catch(error => {
                console.error('更新処理でエラーが発生:', error);
                alert('更新処理中にエラーが発生しました。');
            });
        },
    
        deleteItems() {
            const selectedCheckboxes = document.querySelectorAll('.item-checkbox:checked');
            if (selectedCheckboxes.length === 0) {
                alert('削除する備品を選択してください。');
                return;
            }
    
            if (!confirm('選択した備品を削除しますか？')) {
                return;
            }
    
            // デバッグ用のログ出力
            console.log('削除対象の備品:', selectedCheckboxes);
    
            const formData = new FormData();
            formData.append('delete_items', 'true');
    
            Array.from(selectedCheckboxes).forEach(checkbox => {
                const itemNumber = checkbox.getAttribute('data-item-number');
                formData.append('item_numbers[]', itemNumber);
                console.log('削除する備品番号:', itemNumber);
            });
    
            fetch(window.location.href, {
                method: 'POST',
                body: formData
            })
            .then(response => {
                console.log('サーバーレスポンス:', response);
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.text();
            })
            .then(() => {
                window.location.reload();
            })
            .catch(error => {
                console.error('削除処理でエラーが発生:', error);
                alert('削除処理中にエラーが発生しました。');
            });
        }

    //     //この下で更新または削除
    //     updateItems() {
    //         const selectedCheckboxes = document.querySelectorAll('.item-checkbox:checked');
    //         if (selectedCheckboxes.length === 0) {
    //             alert('更新する備品を選択してください。');
    //             return;
    //         }
    
    //         if (!confirm('選択した備品を更新しますか？')) {
    //             return;
    //         }
    // ///////////////////////////////////////////



    // /////////////////////
    //         const updateData = Array.from(selectedCheckboxes).map(checkbox => {
    //             const row = checkbox.closest('tr');
    //             return {
    //                 item_number: checkbox.getAttribute('data-item-number'),
    //                 item_name: row.querySelector('.item-name-input').value,
    //                 item_total: row.querySelector('.item-total-input').value,
    //                 item_price: row.querySelector('.item-price-input').value
    //             };
    //         });
    
    //         const form = document.createElement('form');
    //         form.method = 'POST';
            
    //         const currentUrl = new URL(window.location.href);
    //         form.action = `${currentUrl.pathname}${currentUrl.search}`;
    
    //         form.appendChild(FacilityManager.utils.createHiddenInput('update_items', 'true'));
    //         updateData.forEach((data) => {
    //             form.appendChild(FacilityManager.utils.createHiddenInput(`item_numbers[]`, data.item_number));
    //             form.appendChild(FacilityManager.utils.createHiddenInput(`item_names[]`, data.item_name));
    //             form.appendChild(FacilityManager.utils.createHiddenInput(`item_totals[]`, data.item_total));
    //             form.appendChild(FacilityManager.utils.createHiddenInput(`item_prices[]`, data.item_price));
    //         });
    
    //         document.body.appendChild(form);
    //         form.submit();
    //     },

    //     deleteItems() {
    //         const selectedCheckboxes = document.querySelectorAll('.item-checkbox:checked');
            
    //         if (selectedCheckboxes.length === 0) {
    //             alert('削除する備品を選択してください。');
    //             return;
    //         }
            
    //         if (!confirm('選択した備品を削除しますか？')) {
    //             return;
    //         }
            
    //         const formData = new FormData();
    //         formData.append('delete_items', 'true');
            
    //         Array.from(selectedCheckboxes).forEach(checkbox => {
    //             formData.append('item_numbers[]', checkbox.getAttribute('data-item-number'));
    //         });
            
    //         fetch(window.location.href, {
    //             method: 'POST',
    //             body: formData
    //         })
    //         .then(() => {
    //             window.location.reload();
    //         })
    //         .catch(error => {
    //             console.error('削除処理でエラーが発生しました:', error);
    //         });
    //     }
    
        // deleteItems() {
        //     const selectedCheckboxes = document.querySelectorAll('.item-checkbox:checked');
        //     if (selectedCheckboxes.length === 0) {
        //         alert('削除する備品を選択してください。');
        //         return;
        //     }
    
        //     if (!confirm('選択した備品を削除しますか？')) {
        //         return;
        //     }
    
        //     const itemNumbers = Array.from(selectedCheckboxes).map(checkbox => 
        //         checkbox.getAttribute('data-item-number')
        //     );
    
        //     const form = document.createElement('form');
        //     form.method = 'POST';
            
        //     const currentUrl = new URL(window.location.href);
        //     form.action = `${currentUrl.pathname}${currentUrl.search}`;
    
        //     form.appendChild(FacilityManager.utils.createHiddenInput('delete_items', 'true'));
        //     itemNumbers.forEach(number => {
        //         form.appendChild(FacilityManager.utils.createHiddenInput('item_numbers[]', number));
        //     });
    
        //     document.body.appendChild(form);
        //     form.submit();
        // }
    
    }
};

// // デバッグ
// console.log(createItemHiddenInput(FacilityManager.items.form));


/////////////////////////////////////////////////////
