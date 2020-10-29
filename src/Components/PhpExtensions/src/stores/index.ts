import conf from '@/Helper/src/components/conf'
import { configure } from 'mobx'

configure({
  enforceActions: 'observed',
})

class Store {
  public readonly ID = 'phpExtensions'
  public readonly conf = conf?.[this.ID]
  public readonly enabled: boolean = !!this.conf
}

const PhpExtensionsStore = new Store()

export default PhpExtensionsStore
