import { configure, makeAutoObservable } from 'mobx';
import { isDeepEqual } from '@/Components/Utils/components/is-deep-equal/index.ts';
import type { ServerInfoPollDataProps } from './typings.ts';configure({
  enforceActions: 'observed',
});
class Main {
  pollData: ServerInfoPollDataProps | null = null;
  publicIpv4 = '';
  publicIpv6 = '';
  constructor() {
    makeAutoObservable(this);
  }
  setPollData = (pollData: ServerInfoPollDataProps | null) => {
    if (isDeepEqual(pollData, this.pollData)) {
      return;
    }
    this.pollData = pollData;
  };
  setPublicIpv4 = (ipv4: string) => {
    this.publicIpv4 = ipv4;
  };
  setPublicIpv6 = (ipv6: string) => {
    this.publicIpv6 = ipv6;
  };
}
export const ServerInfoStore = new Main();
