import { get } from 'lodash-es'
import conf from '~components/Helper/src/components/conf'
import { observable, action } from 'mobx'

class BootstrapStore {
  public ID = 'bootstrap'
  public conf = get(conf, this.ID)
  public version: string = get(this.conf, 'version')
  public changelogUrl: string = get(this.conf, 'changelogUrl')
  public appName: string = get(this.conf, 'appName')
  public appUrl: string = get(this.conf, 'appUrl')
  public authorUrl: string = get(this.conf, 'authorUrl')
  public authorName: string = get(this.conf, 'authorName')

  @observable public appContainer: HTMLElement | null = null

  @action
  public setAppContainer = (appContainer: HTMLElement | null) => {
    this.appContainer = appContainer
  }
}

export default new BootstrapStore()
