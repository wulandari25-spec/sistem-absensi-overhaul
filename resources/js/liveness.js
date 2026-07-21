class LivenessChallenge {
    constructor(videoEl, options = {}) {
        this.video = videoEl;
        this.threshold = options.threshold || 15; // pixel pergeseran minimum
        this.durasiDetikChallenge = options.durasi || 4;
    }

    pilihChallengeAcak() {
        const daftar = [
            { instruksi: 'Silakan hadap ke KIRI', arah: 'kiri' },
            { instruksi: 'Silakan hadap ke KANAN', arah: 'kanan' },
        ];
        return daftar[Math.floor(Math.random() * daftar.length)];
    }

    async ambilPosisiHidung() {
        const hasil = await faceapi
            .detectSingleFace(this.video, new faceapi.TinyFaceDetectorOptions())
            .withFaceLandmarks();

        if (!hasil) return null;

        const noseTip = hasil.landmarks.getNose()[3]; // titik ujung hidung
        return { x: noseTip.x, y: noseTip.y };
    }

    async jalankan(onStatusUpdate) {
        const challenge = this.pilihChallengeAcak();
        onStatusUpdate(`${challenge.instruksi} dalam ${this.durasiDetikChallenge} detik...`);

        const posisiAwal = await this.ambilPosisiHidung();
        if (!posisiAwal) {
            return { live: false, alasan: 'Wajah tidak terdeteksi di awal.' };
        }

        await new Promise(resolve => setTimeout(resolve, this.durasiDetikChallenge * 1000));

        const posisiAkhir = await this.ambilPosisiHidung();
        if (!posisiAkhir) {
            return { live: false, alasan: 'Wajah tidak terdeteksi di akhir.' };
        }

        const pergeseranX = posisiAkhir.x - posisiAwal.x;

        // Validasi arah pergeseran sesuai instruksi
        const sesuaiArah =
            (challenge.arah === 'kiri' && pergeseranX < -this.threshold) ||
            (challenge.arah === 'kanan' && pergeseranX > this.threshold);

        if (!sesuaiArah) {
            return { live: false, alasan: 'Gerakan tidak sesuai instruksi (kemungkinan foto statis).' };
        }

        return { live: true, arah: challenge.arah, pergeseran: pergeseranX };
    }
}