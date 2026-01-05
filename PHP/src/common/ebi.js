document.addEventListener('DOMContentLoaded', function() {

    //ラジオボタンの当たり判定をtrに拡大
    const tbody = document.querySelector('table tbody');
        
    tbody.addEventListener('click', function(e) {
        // クリックされた要素がtr要素の子孫である場合
        const tr = e.target.closest('tr');
        if (tr) {
            // tr内のラジオボタンを取得
            const radio = tr.querySelector('input[type="radio"]');
            if (radio) {
                radio.checked = true;
            }
        }
    });
});