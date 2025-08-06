import { conf } from '../../Utils/components/conf';

class Main {
  readonly id = 'bootstrap';
  readonly conf = conf?.[this.id];
  readonly version: string = String(this.conf?.version ?? '0.0.0');
  readonly appConfigUrls: string[] = this.conf?.appConfigUrls ?? [];
  readonly appConfigUrlDev: string = String(this.conf?.appConfigUrlDev ?? '');
  readonly appName: string = String(this.conf?.appName ?? '');
  readonly appUrl: string = String(this.conf?.appUrl ?? '');
  readonly authorUrl: string = String(this.conf?.authorUrl ?? '');
  readonly authorName: string = String(this.conf?.authorName ?? '');
  readonly isDev = Boolean(this.conf?.isDev ?? false);
}
export const BootstrapConstants = new Main();
