import conf from '~components/Helper/src/components/conf'
import { computed, configure, observable, action, toJS } from 'mobx'
import { DataProps } from '~components/Fetch/src/stores'

configure({
  enforceActions: 'observed',
})

export interface NodesItemProps {
  id: string
  url: string
  fetchUrl: string
  isLoading: boolean
  isError: boolean
  errMsg: string
  data: DataProps
}

class NodesStore {
  public readonly ID = 'nodes'
  public readonly conf = conf?.[this.ID]
  public readonly enabled: boolean = !!this.conf

  public readonly DEFAULT_ITEM = {
    id: '',
    url: '',
    isLoading: true,
    isError: false,
    fetchUrl: '',
  }

  @observable public items: NodesItemProps[] = []

  public constructor() {
    const items = (this.conf?.items || []).map(({ url, ...props }) => {
      return {
        ...this.DEFAULT_ITEM,
        ...{
          url,
          fetchUrl: `${url}?action=fetch`,
          ...props,
        },
      }
    })
    this.setItems(items)
  }

  @action
  public setItems = (items: NodesItemProps[]) => {
    this.items = items
  }

  @action
  public setItem = ({ id, ...props }: Partial<NodesItemProps>) => {
    const i = this.items.findIndex(item => item.id === id)

    if (i === -1) {
      return
    }

    this.items[i] = { ...toJS(this.items[i]), ...props }
  }

  @computed
  public get itemsCount() {
    return this.items.length
  }
}

export default new NodesStore()
