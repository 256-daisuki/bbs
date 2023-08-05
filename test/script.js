document.addEventListener("DOMContentLoaded", () => {
    const popupContainer = document.querySelector(".popup-container");

    setTimeout(() => {
        popupContainer.style.top = "10px"; // 上から表示するために位置を0に設定
        setTimeout(() => {
            closePopup();
        }, 2000);
    }, 1500);
});


const popupContainer = document.querySelector(".popup-container");

closeBtn.addEventListener("click", () => {
    closePopup();
});

function closePopup() {
    popupContainer.classList.add("closing"); // クラスを追加
    setTimeout(() => {
        popupContainer.classList.remove("closing"); // クラスを削除
        popupContainer.style.top = "-100%"; // 画面外に戻すために位置を-100%に設定
    }, 2000); // 2秒後に実行
}
