import sharp from 'sharp';
import fs from 'fs';
import path from 'path';

const SRC = 'C:/Users/82103/Desktop/sam/이미지';
const OUT = 'storage/app/public/farm';

fs.mkdirSync(OUT, { recursive: true });

const files = fs.readdirSync(SRC)
    .filter(f => /\.jpe?g$/i.test(f))
    .sort();

console.log(`원본 ${files.length}장 처리 시작`);

for (let i = 0; i < files.length; i++) {
    const n = String(i + 1).padStart(2, '0');
    const input = path.join(SRC, files[i]);

    // 대형(히어로/라이트박스용) - EXIF 자동회전 + 1600px + 압축
    await sharp(input)
        .rotate()
        .resize({ width: 1600, withoutEnlargement: true })
        .jpeg({ quality: 80, mozjpeg: true })
        .toFile(path.join(OUT, `farm-${n}.jpg`));

    // 갤러리 썸네일 - 정사각 700 크롭
    await sharp(input)
        .rotate()
        .resize({ width: 700, height: 700, fit: 'cover', position: 'centre' })
        .jpeg({ quality: 78, mozjpeg: true })
        .toFile(path.join(OUT, `farm-${n}-thumb.jpg`));

    process.stdout.write(`  farm-${n} `);
}

// 영상 포스터 (선명한 열매 클로즈업 사용: 원본 9번째 = index 8)
const posterSrc = path.join(SRC, files[Math.min(8, files.length - 1)]);
await sharp(posterSrc)
    .rotate()
    .resize({ width: 1280 })
    .jpeg({ quality: 82, mozjpeg: true })
    .toFile(path.join(OUT, 'farm-video-poster.jpg'));

console.log(`\n완료`);
