import { observable, action, computed, configure } from 'mobx'
import conf from '~components/Helper/src/components/conf'

configure({
  enforceActions: 'observed',
})

export interface PingItemProps {
  time: number
}

class MyInfoStore {
  public readonly ID = 'myInfo'
  public readonly conf = conf?.[this.ID]
  public readonly enabled: boolean = !!this.conf
}

export default new MyInfoStore()
