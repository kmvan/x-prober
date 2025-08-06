import { conf } from '@/Components/Utils/components/conf';

class Main {
  readonly id = 'networkStats';
  readonly conf = conf?.[this.id];
  readonly isEnable = Boolean(this.conf);
}
export const NetworkStatsConstants = new Main();
