var SolutionFinder =
	new function()
	{
		var pnlSolutionFinder;
		var pnlWants;
		var pnlHaves;

		var solutionsData;

		var isInitialized;

		function LogActivity(text)
		{
			fetch("SolutionFinderLog?" + text, (r) => { });
		}

		function CreateButton(label, clickHandler)
		{
			var cmdButton = document.createElement("p");

			cmdButton.className = "Button";
			cmdButton.innerText = label;
			cmdButton.onclick =
				() =>
				{
					LogActivity("Click=" + label);
					clickHandler(cmdButton);
				};

			return cmdButton;
		}

		function FindTable(element)
		{
			while (element != null)
			{
				if (element.tagName == "TABLE")
					break;

				element = element.parentElement;
			}

			return element;
		}

		function LoadMenuOptions(pnlMenu, options, optionCallback)
		{
			while (pnlMenu.firstChild != null)
				pnlMenu.removeChild(pnlMenu.firstChild);

			for (let [option, data] of Object.entries(options))
				pnlMenu.appendChild(CreateButton(option, (cmdOption) => { MarkButtonSelected(cmdOption); optionCallback(data); }));

			if (isInitialized)
			{
				var container = FindTable(pnlMenu);

				if (container != null)
					container.scrollIntoView({ behavior: "smooth" });
			}
		}

		function MarkButtonSelected(cmdButton)
		{
			var cmdOtherButton = cmdButton.parentElement.firstChild;

			while (cmdOtherButton != null)
			{
				cmdOtherButton.classList.remove("Selected");
				cmdOtherButton = cmdOtherButton.nextSibling;
			}

			cmdButton.classList.add("Selected");
		}

		function LoadHaves(solutionSelectedCallback)
		{
			LoadMenuOptions(pnlHaves, solutionsData, wants => LoadWants(wants, solutionSelectedCallback));
		}

		function LoadWants(wants, solutionSelectedCallback)
		{
			LoadMenuOptions(pnlWants, wants, solutionSelectedCallback);
		}

		function InitializeSolutionsFailed()
		{
			pnlSolutionFinder.style.display = "none";
		}

		this.InitializeComponents =
			function InitializeComponents(rootElement)
			{
				pnlSolutionFinder = rootElement;

				pnlWants = pnlSolutionFinder.querySelector("#pnlWants");
				pnlHaves = pnlSolutionFinder.querySelector("#pnlHaves");
			};

		this.InitializeSolutions =
			function InitializeSolutions(solutionSelectedCallback)
			{
				fetch("solutions.json").then(
					async response =>
					{
						if (response.ok)
						{
							response.json()
								.then(
									(data) =>
									{
										solutionsData = data;
										LoadHaves(solutionSelectedCallback);

										isInitialized = true;
									})
								.catch(() => InitializeSolutionsFailed());
						}
						else
							InitializeSolutionsFailed();
					});
			};
	};

function InitializeSolutionFinder(pnlSolutionFinder, solutionSelectedCallback)
{
	SolutionFinder.InitializeComponents(pnlSolutionFinder);
	SolutionFinder.InitializeSolutions(solutionSelectedCallback);
}
