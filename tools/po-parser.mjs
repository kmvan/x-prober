/**
 * @version 1.0.1
 */

import { existsSync, readFileSync } from 'node:fs';
import gettextParser from 'gettext-parser';

const PARSE_REGEX =
  /(msgctxt\s+"(.+?)"\s+)?msgid\s+"(.+?)"\s+msgstr\s+"(.+?)"/gm;
export class PoParser {
  poPath = '';
  items = {};
  constructor({ poPath }) {
    this.poPath = poPath;
    if (!existsSync(this.poPath)) {
      throw new Error(`${this.poPath} not exists`);
    }
  }
  parse = () => {
    const input = readFileSync(this.poPath);
    const po = gettextParser.po.parse(input);
    for (const group of Object.values(po.translations)) {
      for (const item of Object.values(group)) {
        const id = item.msgid;
        const str = item.msgstr[0];
        const ctxt = item?.msgctxt || '';
        const key = ctxt !== '' ? `${ctxt}|${id}` : id;
        this.items[key] = str;
      }
    }
    this.items = Object.keys(this.items)
      .sort()
      .reduce((r, k) => {
        r[k] = this.items[k];
        return r;
      }, {});
    return this.items;
  };
}
