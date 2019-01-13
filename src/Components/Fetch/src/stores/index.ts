import { observable, action } from 'mobx'
import fetchServer from '~components/Helper/src/components/fetch-server'

class FetchStore {
  @observable public isLoading: boolean = true
  @observable public data = {}

  constructor() {
    this.initFetch()
  }

  public initFetch = async () => {
    const res = await fetchServer({
      action: 'fetch',
    })

    if (res && res.code === 0) {
      this.setData(res.data)
      this.isLoading && this.setIsLoading(false)

      setTimeout(async () => {
        await this.initFetch()
      }, 1000)
    }
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
