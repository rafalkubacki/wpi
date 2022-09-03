import '../../styles/components/_lightbox.scss';

export default function lightbox() {
  const links = document.querySelectorAll('a[data-lightbox]');

  const lightboxSheme = `
    <div class="lightbox" id="lightbox" tabindex="-1">
      <div class="lightbox__container">
        <div class="lightbox__header">
          <div class="lightbox__title"></div>
          <button class="lightbox__close">&times;</button>
        </div>
        <div class="lightbox__content"></div>
      </div>
      <button class="lightbox__button lightbox__prev">&larr;</button>
      <button class="lightbox__button lightbox__next">&rarr;</button>
    </div>
  `;

  if (links.length > 0) {
    document.body.insertAdjacentHTML('beforeend', lightboxSheme);
    let current = -1;
    let currentAlbum = null;
    const albums = {};
    const l = document.querySelector('#lightbox');
    const lContent = l.querySelector('.lightbox__content');
    const lTitle = l.querySelector('.lightbox__title');
    const lClose = l.querySelector('.lightbox__close');
    const lPrev = l.querySelector('.lightbox__prev');
    const lNext = l.querySelector('.lightbox__next');

    const open = () => {
      if (!l.classList.contains('active')) {
        l.classList.add('active');
        document.body.style.overflow = 'hidden';
      }
    }

    const close = () => {
      if (l.classList.contains('active')) {
        l.classList.remove('active');
        document.body.style.overflow = null;
      }
    }

    const change = () => {
      const url = albums[currentAlbum][current].href;
      lContent.innerHTML = `<img src="${url}" alt="Lightbox" />`;
      if (albums[currentAlbum][current].dataset.title) {
        lTitle.innerHTML = albums[currentAlbum][current].dataset.title;
      } else {
        lTitle.innerHTML = null;
      }


      if (albums[currentAlbum].length > 1) {
        lPrev.disabled = null;
        lNext.disabled = null;
      } else {
        lPrev.disabled = true;
        lNext.disabled = true;
      }
    }

    const prev = () => {
      current = current-1 >= 0 ? current-1 : albums[currentAlbum].length-1;
      change();
    }

    const next = () => {
      current = current+1 < albums[currentAlbum].length ? current+1 : 0;
      change();
    }

    window.onkeydown = (e) => {
      switch (e.keyCode) {
        case 27:
          close();
          break;

        case 37:
          prev();
          break;

        case 39:
          next();
          break;
      }
    }

    lPrev.onclick = () => prev();

    lNext.onclick = () => next();

    lClose.onclick = () => close();

    l.onclick = (e) => {
      if (e.target !== l)
        return;

      close();
    }

    Array.from(links).map((link, i) => {
      const album = link.dataset.lightbox;

      if (!albums[album]) {
        albums[album] = [];
      }

      const number = albums[album].length;
      albums[album].push(link);

      link.onclick = (e) => {
        e.preventDefault();
        currentAlbum = album;
        current = number;
        change();
        open();
      }
    });
  }
}
