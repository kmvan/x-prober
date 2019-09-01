import { observable, action, computed, configure } from 'mobx'
import { template } from 'lodash-es'
import { gettext } from '~components/Language/src'
import BootstrapStore from '~components/Bootstrap/src/stores'
import ConfigStore from '~components/Config/src/stores'
import versionCompare from '~components/Helper/src/components/version-compare'

configure({
  enforceActions: 'observed',
})

class UpdaterStore {
  @observable public isUpdating: boolean = false
  @observable public isUpdateError: boolean = false

  @computed
  get newVersion(): string {
    const { appConfig } = ConfigStore

    if (!appConfig || !appConfig.APP_VERSION) {
      return ''
    }

    return versionCompare(BootstrapStore.version, appConfig.APP_VERSION) === -1
      ? appConfig.APP_VERSION
      : ''
  }

  @action
  public setIsUpdating = (isUpdating: boolean) => {
    this.isUpdating = isUpdating
  }

  @action
  public setIsUpdateError = (isUpdateError: boolean) => {
    this.isUpdateError = isUpdateError
  }

  @computed
  get notiText(): string {
    if (this.isUpdating) {
      return gettext('⏳ Updating, please wait a second...')
    }

    if (this.isUpdateError) {
      return gettext('❌ Update error, click here to try again?')
    }

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

    return ''
  }
}

export default new UpdaterStore()
