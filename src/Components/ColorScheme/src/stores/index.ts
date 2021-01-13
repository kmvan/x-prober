import schemes from './colors'
import {
  action,
  computed,
  configure,
  makeObservable,
  observable
  } from 'mobx'
configure({
  enforceActions: 'observed',
})
export interface ColorSchemeProps {
  colorDark: string
  colorDarkDeep: string
  colorGray: string
  colorDownload: string
  colorUpload: string
  textShadowWithDarkBg: string
  textShadowWithLightBg: string
  colorDarkRgb: string
}
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
  public get scheme(): ColorSchemeProps {
    return schemes?.[this.schemeId] || schemes.default
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
