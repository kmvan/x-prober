import { get } from 'lodash-es'
import conf from '~components/Helper/src/components/conf'
import { observable, action, configure } from 'mobx'

configure({
  enforceActions: 'observed',
})

class BootstrapStore {
  public readonly ID = 'bootstrap'
  public readonly conf = get(conf, this.ID)
  public version: string = get(this.conf, 'version')
  public appConfigUrls: string[] = get(this.conf, 'appConfigUrls')
  public appConfigUrlDev: string = get(this.conf, 'appConfigUrlDev')
  public appName: string = get(this.conf, 'appName')
  public appUrl: string = get(this.conf, 'appUrl')
  public authorUrl: string = get(this.conf, 'authorUrl')
  public authorName: string = get(this.conf, 'authorName')
  public isDev: boolean = get(this.conf, 'isDev')

  @observable public appContainer: HTMLElement | null = null

  @action
  public setAppContainer = (appContainer: HTMLElement | null) => {
    this.appContainer = appContainer
  }
}

export default new BootstrapStore()
