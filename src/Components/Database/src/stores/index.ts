import conf from '@/Helper/src/components/conf'
import { configure } from 'mobx'

configure({
  enforceActions: 'observed',
})

class DatabaseStore {
  public readonly ID = 'database'
  public readonly conf = conf?.[this.ID]
  public readonly enabled: boolean = !!this.conf
}

export default new DatabaseStore()
