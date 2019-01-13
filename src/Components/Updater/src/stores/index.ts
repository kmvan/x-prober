import { observable, action } from 'mobx'
import conf from '~components/Helper/src/components/conf'

class UpdaterStore {
  public ID = 'updater'
  public conf = conf[this.ID] || false

  @observable public title: any = ''
  @observable public isLoading: boolean = false
  @observable public newVersion: string = this.conf.version

  @action public setNewVersion = (newVersion: string) => {
    this.newVersion = newVersion
  }

  @action
  public setTitle = (title: any) => {
    this.title = title
  }

  @action
  public setIsLoading = (isLoading: boolean) => {
    this.isLoading = isLoading
  }
}

export default new UpdaterStore()
