document.addEventListener('click', async (e) => {
  const btn = e.target.closest('.favorite-btn');
  if (!btn) return;

  const token = document
    .querySelector('meta[name="csrf-token"]')
    ?.getAttribute('content');

  const isFavorited = btn.dataset.favorited === '1';

  const url = isFavorited
    ? btn.dataset.unfavoriteUrl
    : btn.dataset.favoriteUrl;

  const method = isFavorited ? 'DELETE' : 'POST';

  try {
    const res = await fetch(url, {
      method,
      headers: {
        'X-CSRF-TOKEN': token,
        'Accept': 'application/json',
      },
    });

    if (!res.ok) {
      const text = await res.text();
      console.error(res.status, text);
      alert(`お気に入りの更新ができません (${res.status})`);
      return;
    }

    const data = await res.json();

    // 状態更新
    btn.dataset.favorited = data.favorited ? '1' : '0';
    const heart = btn.querySelector('.favorite-heart');
    heart.style.color = data.favorited ? 'red' : '#999';
  } catch (err) {
    console.error(err);
    alert('お気に入りの更新ができません');
  }
});
