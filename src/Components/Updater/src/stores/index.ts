import { observable, action, computed } from 'mobx'
import { template } from 'lodash-es'
import { gettext } from '~components/Language/src'
import BootstrapStore from '~components/Bootstrap/src/stores'

class UpdaterStore {
  @observable public newVersion: string = ''
  @observable public isUpdating: boolean = false

  @action public setNewVersion = (newVersion: string) => {
    this.newVersion = newVersion
  }

  @action
  public setIsUpdating = (isUpdating: boolean) => {
    this.isUpdating = isUpdating
  }

  @computed
  get notiText(): string {
    if (this.newVersion) {
      return template(
        gettext(
          '✨ Found update! Version <%= oldVersion %> → <%= newVersion %>'
        )
      )({
        oldVersion: BootstrapStore.version,
        newVersion: this.newVersion,
      })
    }

    if (this.isUpdating) {
      return gettext('⏳ Updating...')
    }

    return ''
  }
}

export default new UpdaterStore()
