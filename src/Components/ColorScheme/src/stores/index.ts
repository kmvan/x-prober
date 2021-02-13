import { action, computed, configure, makeObservable, observable } from 'mobx'
import colorSchemes from './color-schemes'
configure({
  enforceActions: 'observed',
})
class Store {
  public readonly ID = 'colorScheme'
  private readonly STORAGE_ID = 'schemeId'
  @observable public schemeId: string = this.getStorageSchemeId()
  public constructor() {
    makeObservable(this)
  }
  @action
  public setSchemeId = (schemeId: string) => {
    this.schemeId = schemeId
    this.setStorageSchemeId(schemeId)
  }
  @computed
  public get scheme() {
    return colorSchemes?.[this.schemeId] ?? colorSchemes.default
  }
  private getStorageSchemeId(): string {
    return localStorage.getItem(this.STORAGE_ID) || 'default'
  }
  private setStorageSchemeId = (schemeId: string) => {
    localStorage.setItem(this.STORAGE_ID, schemeId)
  }
}
const ColorSchemeStore = new Store()
export default ColorSchemeStore
