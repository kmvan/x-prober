import { conf } from '../Utils/components/conf'
class Main {
  public readonly id = 'phpExtensions'
  public readonly conf = conf?.[this.id]
  public readonly isEnable = Boolean(this.conf)
}
export const PhpExtensionsConstants = new Main()
