import conf from '~components/Helper/src/components/conf'
import { configure } from 'mobx'

configure({
  enforceActions: 'observed',
})

class DatabaseStore {
  public readonly ID = 'database'
  public readonly conf = conf?.[this.ID]
}

export default new DatabaseStore()
