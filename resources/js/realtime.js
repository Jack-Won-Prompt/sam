// 실시간(Pusher) — 키가 있을 때만 활성화. 없으면 폴링으로 폴백.
import Pusher from 'pusher-js';

const KEY = import.meta.env.VITE_PUSHER_APP_KEY;
const CLUSTER = import.meta.env.VITE_PUSHER_APP_CLUSTER || 'ap3';

let pusher = null;
if (KEY) {
    pusher = new Pusher(KEY, { cluster: CLUSTER, forceTLS: true });
    window.__pusher = pusher;
}

window.hasRealtime = !!pusher;

// channelName 구독 → 'message' 이벤트 콜백. 미설정 시 null 반환(폴링 사용)
window.subscribeChat = function (channelName, onMessage) {
    if (!pusher) return null;
    const ch = pusher.subscribe(channelName);
    ch.bind('message', onMessage);
    return ch;
};

window.unsubscribeChat = function (channelName) {
    if (pusher) pusher.unsubscribe(channelName);
};
