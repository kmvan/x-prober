import { configure, makeAutoObservable } from 'mobx';
import { isDeepEqual } from '@/Components/Utils/components/is-deep-equal/index.ts';
import type { PhpExtensionsPollDataProps } from './typings.ts';configure({
  enforceActions: 'observed',
});
class Main {
  pollData: PhpExtensionsPollDataProps | null = null;
  constructor() {
    makeAutoObservable(this);
  }
  setPollData = (pollData: PhpExtensionsPollDataProps | null) => {
    if (isDeepEqual(pollData, this.pollData)) {
      return;
    }
    this.pollData = pollData;
  };
}
export const PhpExtensionsStore = new Main();
