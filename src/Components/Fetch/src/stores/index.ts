import { observable, action, configure } from 'mobx'
import restfulFetch from '~components/Fetch/src/restful-fetch'
import { OK } from '~components/Restful/src/http-status'
import { gettext } from '~components/Language/src'

configure({
  enforceActions: 'observed',
})

class FetchStore {
  @observable public isLoading: boolean = true
  @observable public data = {}

  constructor() {
    this.initFetch()
  }

  public initFetch = async () => {
    await restfulFetch('fetch')
      .then(([{ status }, data]) => {
        if (status === OK) {
          this.setData(data)
          this.isLoading && this.setIsLoading(false)
          setTimeout(async () => {
            await this.initFetch()
          }, 1000)
        }
      })
      .catch(err => {
        alert(gettext('Fetch error, please refresh page.'))
      })
  }

  @action
  public setIsLoading = (isLoading: boolean) => {
    this.isLoading = isLoading
  }

  @action
  public setData = data => {
    this.data = data
  }
}

export default new FetchStore()
