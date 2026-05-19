(function () {
    'use strict';

    // Функция определения типа устройства на основе User-Agent
    function getDeviceType() {
        const ua = navigator.userAgent;
        if (/(tablet|ipad|playbook|silk)|(android(?!.*mobi))/i.test(ua)) {
            return "Tablet";
        }
        if (/Mobile|iP(hone|od)|Android|BlackBerry|IEMobile|Kindle|Silk-Accelerated|(hpw|web)OS|Opera M(obi|ini)/i.test(ua)) {
            return "Mobile";
        }
        return "Desktop";
    }

    // Собираем доступные на клиенте метрики устройства
    const trackingData = {
        device: getDeviceType(),
        screen_resolution: `${window.screen.width}x${window.screen.height}`,
        language: navigator.language || navigator.userLanguage,
        current_url: window.location.href
    };

    // Определяем адрес бэкенда относительно того, откуда загружен скрипт
    // Это позволит коду работать без жестко зашитых URL на любом хостинге
    const scriptElement = document.currentScript;
    const backendUrl = scriptElement
        ? new URL(scriptElement.src).origin + '/api/visit'
        : 'http://http://127.0.0.1:8000/api/visit';

    // Отправляем данные на сервер.
    // Используем встроенный Fetch API с флагом keepalive, чтобы запрос гарантированно
    // дошел, даже если пользователь мгновенно закрыл или обновил страницу.
    fetch(backendUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify(trackingData),
        keepalive: true
    }).catch(err => console.warn('Counter tracking error:', err));
})();
