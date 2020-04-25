import conf from '~components/Helper/src/components/conf'
import { configure } from 'mobx'

configure({
  enforceActions: 'observed',
})

class PhpExtensionsStore {
  public readonly ID = 'phpExtensions'
  public readonly conf = conf?.[this.ID]
}

export default new PhpExtensionsStore()
