import { conf } from '@/Utils/src/components/conf'
import { configure } from 'mobx'
configure({
  enforceActions: 'observed',
})
export interface PingItemProps {
  time: number
}
class Main {
  public readonly ID = 'myInfo'
  public readonly conf = conf?.[this.ID]
  public readonly enabled: boolean = !!this.conf
}
export const MyInfoStore = new Main()
