import { Controller } from '@hotwired/stimulus';

/* stimulusFetch: 'lazy' */
export default class DepthRailController extends Controller {
    static targets = ['curseur', 'libelle'];

    connect() {
        this.onScroll = this.onScroll.bind(this);
        window.addEventListener('scroll', this.onScroll, { passive: true });
        window.addEventListener('resize', this.onScroll);
        this.onScroll();
    }

    disconnect() {
        window.removeEventListener('scroll', this.onScroll);
        window.removeEventListener('resize', this.onScroll);
    }

    onScroll() {
        if (!window.matchMedia('(min-width: 1400px)').matches) {
            return;
        }

        const document_ = document.documentElement;
        const max = (document_.scrollHeight - window.innerHeight) || 1;
        const ratio = Math.min(1, Math.max(0, window.scrollY / max));
        const profondeur = Math.round(ratio * 300);

        this.curseurTarget.style.top = (ratio * 100).toFixed(2) + '%';
        this.libelleTarget.textContent = profondeur + ' m';
        this.element.dataset.sombre = this.fondSombre() ? 'true' : 'false';
    }

    fondSombre() {
        const point = document.elementFromPoint(60, Math.round(window.innerHeight / 2));
        for (let noeud = point; noeud && noeud !== document.documentElement; noeud = noeud.parentElement) {
            const fond = getComputedStyle(noeud).backgroundColor;
            const composantes = /rgba?\(([^)]+)\)/.exec(fond);
            if (!composantes) {
                continue;
            }

            const valeurs = composantes[1].split(',').map(Number);
            if (valeurs.length > 3 && valeurs[3] === 0) {
                continue;
            }

            return (0.299 * valeurs[0] + 0.587 * valeurs[1] + 0.114 * valeurs[2]) < 140;
        }

        return false;
    }
}
