import { conf } from '../Utils/components/conf'
class Main {
  public readonly id = 'serverStatus'
  public readonly conf = conf?.[this.id]
  public readonly isEnable: boolean = Boolean(this.conf)
}
export const ServerStatusConstants = new Main()
