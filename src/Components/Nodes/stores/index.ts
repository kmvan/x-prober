import { configure, makeAutoObservable, toJS } from 'mobx'
import { DataProps } from '../../Fetch/stores'
import { NodesConstants } from '../constants'
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
const { conf } = NodesConstants
class Main {
  public readonly DEFAULT_ITEM = {
    id: '',
    url: '',
    isLoading: true,
    isError: false,
    fetchUrl: '',
  }
  public items: NodesItemProps[] = []
  public constructor() {
    makeAutoObservable(this)
    const items = (conf?.items ?? []).map(({ url, ...props }) => ({
      ...this.DEFAULT_ITEM,
      ...{
        url,
        fetchUrl: `${url}?action=fetch`,
        ...props,
      },
    }))
    this.setItems(items)
  }
  public setItems = (items: NodesItemProps[]) => {
    this.items = items
  }
  public setItem = ({ id, ...props }: Partial<NodesItemProps>) => {
    const i = this.items.findIndex((item) => item.id === id)
    if (i === -1) {
      return
    }
    this.items[i] = { ...toJS(this.items[i]), ...props }
  }
  public get itemsCount() {
    return this.items.length
  }
}
export const NodesStore = new Main()
