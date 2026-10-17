import { readdirSync, readFileSync, writeFileSync } from "node:fs";
import { relative, join } from "node:path";
import { deflateRawSync } from "node:zlib";

const CRC_TABLE = new Uint32Array(256);
for (let index = 0; index < 256; index += 1) {
    let value = index;
    for (let bit = 0; bit < 8; bit += 1) {
        value = (value & 1) === 1 ? (0xedb88320 ^ (value >>> 1)) : (value >>> 1);
    }
    CRC_TABLE[index] = value >>> 0;
}

/**
 * @param {Buffer} buffer
 * @returns {number}
 */
function checksum(buffer) {
    let crc = 0xffffffff;
    for (const byte of buffer) {
        crc = CRC_TABLE[(crc ^ byte) & 0xff] ^ (crc >>> 8);
    }
    return (crc ^ 0xffffffff) >>> 0;
}

/**
 * @param {string} directory
 * @param {string[]} files
 */
function collectFiles(directory, files) {
    for (const entry of readdirSync(directory, { withFileTypes: true })) {
        const path = join(directory, entry.name);
        if (entry.isDirectory()) {
            collectFiles(path, files);
            continue;
        }
        if (entry.isFile()) {
            files.push(path);
        }
    }
}

/**
 * Write a real ZIP archive. GNU tar on Ubuntu keeps the tar container even when the file ends in .zip.
 *
 * @param {string} stage Directory that contains folderName.
 * @param {string} folderName Root directory stored in the archive.
 * @param {string} output Destination .zip path.
 * @returns {string[]} Archive entry names.
 */
export function writeZip(stage, folderName, output) {
    const files = [];
    collectFiles(join(stage, folderName), files);
    const localParts = [];
    const centralParts = [];
    let offset = 0;

    for (const file of files) {
        const data = readFileSync(file);
        const compressed = deflateRawSync(data);
        const name = relative(stage, file).replace(/\\/g, "/");
        const nameBuffer = Buffer.from(name);
        const crc = checksum(data);
        const local = Buffer.alloc(30);
        local.writeUInt32LE(0x04034b50, 0);
        local.writeUInt16LE(20, 4);
        local.writeUInt16LE(8, 8);
        local.writeUInt16LE(0, 10);
        local.writeUInt16LE(0x54b1, 12);
        local.writeUInt32LE(crc, 14);
        local.writeUInt32LE(compressed.length, 18);
        local.writeUInt32LE(data.length, 22);
        local.writeUInt16LE(nameBuffer.length, 26);
        const central = Buffer.alloc(46);
        central.writeUInt32LE(0x02014b50, 0);
        central.writeUInt16LE(20, 4);
        central.writeUInt16LE(20, 6);
        central.writeUInt16LE(8, 10);
        central.writeUInt16LE(0, 12);
        central.writeUInt16LE(0x54b1, 14);
        central.writeUInt32LE(crc, 16);
        central.writeUInt32LE(compressed.length, 20);
        central.writeUInt32LE(data.length, 24);
        central.writeUInt16LE(nameBuffer.length, 28);
        central.writeUInt32LE(offset, 42);
        localParts.push(local, nameBuffer, compressed);
        centralParts.push(central, nameBuffer);
        offset += local.length + nameBuffer.length + compressed.length;
    }

    const centralDirectory = Buffer.concat(centralParts);
    const end = Buffer.alloc(22);
    end.writeUInt32LE(0x06054b50, 0);
    end.writeUInt16LE(files.length, 8);
    end.writeUInt16LE(files.length, 10);
    end.writeUInt32LE(centralDirectory.length, 12);
    end.writeUInt32LE(offset, 16);
    const archive = Buffer.concat([...localParts, centralDirectory, end]);
    if (archive.length < 4 || archive[0] !== 0x50 || archive[1] !== 0x4b) {
        throw new Error("L'archive produite n'est pas un ZIP");
    }
    writeFileSync(output, archive);
    return files.map((file) => relative(stage, file).replace(/\\/g, "/"));
}
