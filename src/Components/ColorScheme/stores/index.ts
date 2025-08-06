import { configure, makeAutoObservable } from 'mobx';
import { ColorSchemeConstants } from '../constants';
import { colorSchemes } from './color-schemes';

configure({
  enforceActions: 'observed',
});
const { storageId } = ColorSchemeConstants;
class Main {
  schemeId: string = this.getStorageSchemeId();
  constructor() {
    makeAutoObservable(this);
  }
  setSchemeId = (schemeId: string) => {
    this.schemeId = schemeId;
    this.setStorageSchemeId(schemeId);
  };
  get scheme() {
    return colorSchemes?.[this.schemeId] ?? colorSchemes.default;
  }
  private getStorageSchemeId(): string {
    return localStorage.getItem(storageId) || 'default';
  }
  private setStorageSchemeId = (schemeId: string) => {
    localStorage.setItem(storageId, schemeId);
  };
}
export const ColorSchemeStore = new Main();
