import conf from '~components/Helper/src/components/conf'
import { observable, action, configure } from 'mobx'

configure({
  enforceActions: 'observed',
})

class Store {
  public readonly ID = 'bootstrap'
  public readonly conf = conf?.[this.ID]
  public readonly version: string = this.conf?.version
  public readonly appConfigUrls: string[] = this.conf?.appConfigUrls
  public readonly appConfigUrlDev: string = this.conf?.appConfigUrlDev
  public readonly appName: string = this.conf?.appName
  public readonly appUrl: string = this.conf?.appUrl
  public readonly authorUrl: string = this.conf?.authorUrl
  public readonly authorName: string = this.conf?.authorName
  public readonly isDev: boolean = this.conf?.isDev

  @observable public appContainer: HTMLElement | null = null

  @action
  public setAppContainer = (appContainer: HTMLElement | null) => {
    this.appContainer = appContainer
  }
}

const BootstrapStore = new Store()

export default BootstrapStore
