import { conf } from '../Utils/components/conf'
class Main {
  public readonly id = 'nodes'
  public readonly conf = conf?.[this.id]
  public readonly isEnable = Boolean(this.conf)
}
export const NodesConstants = new Main()
