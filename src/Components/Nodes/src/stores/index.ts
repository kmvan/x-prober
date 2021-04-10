import { DataProps } from '@/Fetch/src/stores'
import { conf } from '@/Utils/src/components/conf'
import {
  action,
  computed,
  configure,
  makeObservable,
  observable,
  toJS,
} from 'mobx'
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
class Main {
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
    makeObservable(this)
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
  @action public setItems = (items: NodesItemProps[]) => {
    this.items = items
  }
  @action public setItem = ({ id, ...props }: Partial<NodesItemProps>) => {
    const i = this.items.findIndex((item) => item.id === id)
    if (i === -1) {
      return
    }
    this.items[i] = { ...toJS(this.items[i]), ...props }
  }
  @computed public get itemsCount() {
    return this.items.length
  }
}
export const NodesStore = new Main()
