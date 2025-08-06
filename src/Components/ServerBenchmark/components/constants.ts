import { conf } from '@/Components/Utils/components/conf';

class Main {
  readonly id = 'serverBenchmark';
  readonly conf = conf?.[this.id];
  readonly isEnable: boolean = Boolean(this.conf);
}
export const ServerBenchmarkConstants = new Main();
