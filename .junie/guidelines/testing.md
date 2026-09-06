# Testing Guidelines

## Performance and Efficiency
- **Abort Slow Tests**: If a test is running significantly slower than expected (e.g., hanging on an assertion for more than a few seconds without progress), it must be aborted immediately.
- **Investigation and Fix**: After aborting a slow test, investigate the root cause (e.g., missing elements, race conditions, incorrect selectors) and apply a fix before attempting to run the test again.
- **Avoid Timeouts**: Do not rely on increasing timeouts as a primary fix for slow tests. Focus on making the test execution deterministic and fast.
