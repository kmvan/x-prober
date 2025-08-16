/**
 * @version 1.0.0
 */

import { writeFileSync } from 'node:fs';
import path, { basename, dirname } from 'node:path';
import { fileURLToPath } from 'node:url';
import DeepSort from 'deep-sort-object';
import FastGlob from 'fast-glob';
import { PoParser } from './po-parser.mjs';
import { PotBuilder } from './pot-builder.mjs';

const __dirname = dirname(fileURLToPath(import.meta.url));
class LangBuilder {
  data = {};
  constructor() {
    this.build();
  }
  setData = async () => {
    new PotBuilder({
      potPath: path.resolve(__dirname, '../locales/lang.pot'),
      sourceDir: path.resolve(__dirname, '../src'),
    });
    const files = await FastGlob(path.resolve(__dirname, '../locales/*.po'));
    for (const file of files) {
      const lang = basename(file, '.po');
      const langId = lang.replace('_', '').toLowerCase();
      const parser = new PoParser({
        poPath: path.resolve(__dirname, `../locales/${lang}.po`),
      });
      const items = parser.parse();
      for (const text of Object.keys(items)) {
        if (!this.data[text]) {
          this.data[text] = {};
        }
        if (!this.data[text][langId]) {
          this.data[text][langId] = items[text];
        }
      }
    }
  };
  build = async () => {
    await this.setData();
    this.data = DeepSort(this.data);
    const jsonPath = path.resolve(
      __dirname,
      '../src/Components/Language/data.json'
    );
    writeFileSync(jsonPath, JSON.stringify(this.data, null, 2));
  };
}
new LangBuilder();
