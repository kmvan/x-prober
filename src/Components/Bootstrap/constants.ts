import { conf } from '../Utils/components/conf'
class Main {
  public readonly id = 'bootstrap'
  public readonly conf = conf?.[this.id]
  public readonly version: string = String(this.conf?.version ?? '0.0.0')
  public readonly appConfigUrls: string[] = this.conf?.appConfigUrls ?? []
  public readonly appConfigUrlDev: string = String(
    this.conf?.appConfigUrlDev ?? '',
  )
  public readonly appName: string = String(this.conf?.appName ?? '')
  public readonly appUrl: string = String(this.conf?.appUrl ?? '')
  public readonly authorUrl: string = String(this.conf?.authorUrl ?? '')
  public readonly authorName: string = String(this.conf?.authorName ?? '')
  public readonly isDev = Boolean(this.conf?.isDev ?? false)
}
export const BootstrapConstants = new Main()
